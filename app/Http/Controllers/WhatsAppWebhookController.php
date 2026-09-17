<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.webhook_verify_token');

        if ($request->query('hub_mode') === 'subscribe'
            && $request->query('hub_verify_token') === $verifyToken) {
            return response($request->query('hub_challenge'), 200);
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $data = $request->all();

        $entries = $data['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                $value = $change['value'] ?? [];

                // Handle incoming messages
                if (!empty($value['messages'])) {
                    foreach ($value['messages'] as $message) {
                        $this->storeIncomingMessage($message, $value['contacts'] ?? []);
                    }
                }

                // Handle status updates
                if (!empty($value['statuses'])) {
                    foreach ($value['statuses'] as $status) {
                        $this->updateMessageStatus($status);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    protected function storeIncomingMessage(array $message, array $contacts): void
    {
        $contactName = null;
        $from = $message['from'] ?? null;

        if ($from && !empty($contacts)) {
            foreach ($contacts as $contact) {
                if (($contact['wa_id'] ?? null) === $from) {
                    $contactName = $contact['profile']['name'] ?? null;
                    break;
                }
            }
        }

        $type = $message['type'] ?? 'text';
        $body = null;
        $mediaUrl = null;

        switch ($type) {
            case 'text':
                $body = $message['text']['body'] ?? null;
                break;
            case 'image':
            case 'video':
            case 'audio':
            case 'document':
                $body = $message[$type]['caption'] ?? $message[$type]['filename'] ?? "[$type]";
                $mediaId = $message[$type]['id'] ?? null;
                if ($mediaId) {
                    $mediaUrl = $this->downloadAndStoreMedia($mediaId, $type, $message[$type]['filename'] ?? null, $message[$type]['mime_type'] ?? null);
                }
                break;
            default:
                $body = "[$type]";
        }

        try {
            WhatsAppMessage::create([
                'wa_message_id' => $message['id'] ?? null,
                'phone_number' => $from,
                'contact_name' => $contactName,
                'direction' => 'incoming',
                'type' => in_array($type, ['text', 'template', 'image', 'document', 'video', 'audio']) ? $type : 'text',
                'body' => $body,
                'media_url' => $mediaUrl,
                'status' => 'received',
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook store failed: ' . $e->getMessage());
        }
    }

    protected function downloadAndStoreMedia(string $mediaId, string $type, ?string $filename, ?string $mimeType): ?string
    {
        try {
            $token = config('services.whatsapp.token');

            // Step 1: Get the media URL from WhatsApp
            $mediaInfo = Http::withToken($token)
                ->timeout(10)
                ->get("https://graph.facebook.com/v18.0/{$mediaId}");

            if (!$mediaInfo->successful()) {
                return null;
            }

            $downloadUrl = $mediaInfo->json('url');
            if (!$downloadUrl) {
                return null;
            }

            // Step 2: Download the actual file
            $fileResponse = Http::withToken($token)
                ->timeout(30)
                ->get($downloadUrl);

            if (!$fileResponse->successful()) {
                return null;
            }

            // Determine extension
            $extensions = [
                'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
                'video/mp4' => 'mp4', 'audio/ogg' => 'ogg', 'audio/mpeg' => 'mp3',
                'application/pdf' => 'pdf',
            ];
            $ext = $extensions[$mimeType] ?? pathinfo($filename ?? '', PATHINFO_EXTENSION) ?: 'bin';
            $storedFilename = "whatsapp/{$type}/" . uniqid() . ".{$ext}";

            Storage::disk('public')->put($storedFilename, $fileResponse->body());

            return $storedFilename;
        } catch (\Exception $e) {
            Log::error("WhatsApp media download failed: {$e->getMessage()}");
            return null;
        }
    }

    protected function updateMessageStatus(array $status): void
    {
        $waMessageId = $status['id'] ?? null;
        $newStatus = $status['status'] ?? null;

        if (!$waMessageId || !$newStatus) {
            return;
        }

        $statusMap = [
            'sent' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed' => 'failed',
        ];

        if (isset($statusMap[$newStatus])) {
            WhatsAppMessage::where('wa_message_id', $waMessageId)
                ->update(['status' => $statusMap[$newStatus]]);
        }
    }
}
