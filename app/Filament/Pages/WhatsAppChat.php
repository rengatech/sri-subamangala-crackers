<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppApiService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class WhatsAppChat extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected string $view = 'filament.pages.whats-app-chat';
    protected static string|\UnitEnum|null $navigationGroup = 'Customers';
    protected static ?string $title = 'WhatsApp Chat';

    public string $search = '';
    public ?string $selectedPhone = null;
    public string $messageText = '';
    public int $messagesPerPage = 50;
    public $attachment = null;

    public function getContactsProperty()
    {
        if (!Schema::hasTable('whatsapp_messages')) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30);
        }

        return WhatsAppMessage::select(
                'phone_number',
                DB::raw('MAX(contact_name) as contact_name'),
                DB::raw('MAX(created_at) as last_message_at'),
                DB::raw('MAX(id) as last_message_id')
            )
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('phone_number', 'like', "%{$this->search}%")
                       ->orWhere('contact_name', 'like', "%{$this->search}%");
                });
            })
            ->groupBy('phone_number')
            ->orderByDesc('last_message_at')
            ->paginate(30);
    }

    public function getLastMessageForContact(string $phone): ?string
    {
        $msg = WhatsAppMessage::where('phone_number', $phone)
            ->latest()
            ->first();

        return $msg?->body;
    }

    public function getUnreadCount(string $phone): int
    {
        return WhatsAppMessage::where('phone_number', $phone)
            ->where('direction', 'incoming')
            ->where('status', 'received')
            ->count();
    }

    public function selectContact(string $phone): void
    {
        $this->selectedPhone = $phone;
        $this->messagesPerPage = 50;

        // Mark incoming messages as read
        WhatsAppMessage::where('phone_number', $phone)
            ->where('direction', 'incoming')
            ->where('status', 'received')
            ->update(['status' => 'read']);
    }

    public function getSelectedContactNameProperty(): ?string
    {
        if (!$this->selectedPhone) {
            return null;
        }

        return WhatsAppMessage::where('phone_number', $this->selectedPhone)
            ->whereNotNull('contact_name')
            ->latest()
            ->value('contact_name') ?? $this->selectedPhone;
    }

    public function getMessagesProperty()
    {
        if (!$this->selectedPhone) {
            return collect();
        }

        return WhatsAppMessage::where('phone_number', $this->selectedPhone)
            ->latest()
            ->take($this->messagesPerPage)
            ->get()
            ->reverse()
            ->values();
    }

    public function loadMoreMessages(): void
    {
        $this->messagesPerPage += 50;
    }

    public function removeAttachment(): void
    {
        $this->attachment = null;
    }

    public function sendMessage(): void
    {
        if (!$this->selectedPhone) {
            return;
        }

        $hasText = trim($this->messageText) !== '';
        $hasFile = $this->attachment !== null;

        if (!$hasText && !$hasFile) {
            return;
        }

        Log::info('WhatsApp chat: sendMessage called', [
            'to' => $this->selectedPhone,
            'hasText' => $hasText,
            'hasFile' => $hasFile,
            'text' => $hasText ? $this->messageText : null,
            'fileName' => $hasFile ? $this->attachment->getClientOriginalName() : null,
        ]);

        $service = app(WhatsAppApiService::class);

        // Send file if attached
        if ($hasFile) {
            $result = $this->sendFileMessage($service);
            if (!$result) {
                Log::error('WhatsApp chat: file send failed', ['to' => $this->selectedPhone]);
                Notification::make()->danger()->title('Failed to send file')->send();
                return;
            }
            Log::info('WhatsApp chat: file sent successfully', ['to' => $this->selectedPhone]);
            $this->attachment = null;
        }

        // Send text if provided (and no file, or file + caption scenario)
        if ($hasText && !$hasFile) {
            $result = $service->sendTextMessage($this->selectedPhone, $this->messageText);
            if (!$result) {
                Log::error('WhatsApp chat: text send failed', ['to' => $this->selectedPhone, 'text' => $this->messageText]);
                Notification::make()->danger()->title('Failed to send message')->send();
                return;
            }
            Log::info('WhatsApp chat: text sent successfully', ['to' => $this->selectedPhone]);
        }

        $this->messageText = '';
        Notification::make()->success()->title('Message sent')->send();
    }

    protected function sendFileMessage(WhatsAppApiService $service): ?WhatsAppMessage
    {
        $file = $this->attachment;
        $mimeType = $file->getMimeType();
        $fileName = $file->getClientOriginalName();

        Log::info('WhatsApp chat: preparing file upload', [
            'fileName' => $fileName,
            'mimeType' => $mimeType,
            'size' => $file->getSize(),
        ]);

        // Store the file
        $path = $file->store('whatsapp-uploads', 'public');
        $localPath = Storage::disk('public')->path($path);
        $publicUrl = Storage::disk('public')->url($path);

        Log::info('WhatsApp chat: file stored', ['path' => $path, 'publicUrl' => $publicUrl]);

        // Determine media type
        if (str_starts_with($mimeType, 'image/')) {
            $mediaType = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $mediaType = 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            $mediaType = 'audio';
        } else {
            $mediaType = 'document';
        }

        $caption = trim($this->messageText) ?: null;

        Log::info('WhatsApp chat: sending as media type', ['mediaType' => $mediaType, 'caption' => $caption]);

        return $this->sendWhatsAppMedia($service, $mediaType, $publicUrl, $localPath, $fileName, $caption);
    }

    protected function sendWhatsAppMedia(WhatsAppApiService $service, string $mediaType, string $url, string $localPath, string $fileName, ?string $caption): ?WhatsAppMessage
    {
        $baseUrl = 'https://graph.facebook.com/v18.0';
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $token = config('services.whatsapp.token');
        $apiUrl = "{$baseUrl}/{$phoneNumberId}/messages";

        $mediaPayload = ['link' => $url];
        if ($caption && in_array($mediaType, ['image', 'video', 'document'])) {
            $mediaPayload['caption'] = $caption;
        }
        if ($mediaType === 'document') {
            $mediaPayload['filename'] = $fileName;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->selectedPhone,
            'type' => $mediaType,
            $mediaType => $mediaPayload,
        ];

        try {
            Log::info('WhatsApp sending media message', ['to' => $this->selectedPhone, 'type' => $mediaType, 'file' => $fileName]);

            $response = Http::withToken($token)
                ->timeout(15)
                ->post($apiUrl, $payload);

            Log::info('WhatsApp media message response', ['status' => $response->status(), 'body' => $response->json()]);

            $waMessageId = $response->json('messages.0.id');

            return WhatsAppMessage::create([
                'wa_message_id' => $waMessageId,
                'phone_number' => $this->selectedPhone,
                'direction' => 'outgoing',
                'type' => $mediaType,
                'body' => $caption ?? "[{$mediaType}] {$fileName}",
                'media_url' => $url,
                'status' => $response->successful() ? 'sent' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp media message failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getCustomerOrdersUrlProperty(): ?string
    {
        if (!$this->selectedPhone) {
            return null;
        }

        // Strip country code (91) to get 10-digit number
        $localPhone = preg_replace('/^91/', '', $this->selectedPhone);

        $customer = Customer::where('whatsapp_number', $localPhone)->latest()->first();

        if (!$customer) {
            return null;
        }

        return route('filament.admin.resources.customers.edit', $customer);
    }

    public function getProfilePictureUrlProperty(): ?string
    {
        if (!$this->selectedPhone) {
            return null;
        }

        return Cache::remember("wa_profile_pic_{$this->selectedPhone}", now()->addHours(24), function () {
            try {
                $response = Http::withToken(config('services.whatsapp.token'))
                    ->timeout(5)
                    ->get("https://graph.facebook.com/v18.0/{$this->selectedPhone}/profile_picture");

                if ($response->successful()) {
                    return $response->json('data.url') ?? $response->json('profile_pic_url');
                }
            } catch (\Exception $e) {
                // Profile picture not available - silently fail
            }
            return null;
        });
    }

}
