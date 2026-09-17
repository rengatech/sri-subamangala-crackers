<?php

namespace App\Services;

use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppApiService
{
    protected string $baseUrl = 'https://graph.facebook.com/v18.0';

    protected function getPhoneNumberId(): string
    {
        return config('services.whatsapp.phone_number_id');
    }

    protected function getToken(): ?string
    {
        return config('services.whatsapp.token');
    }

    protected function getBusinessAccountId(): string
    {
        return config('services.whatsapp.business_account_id');
    }

    /**
     * Fetch all message templates from Meta API.
     */
    public function getTemplates(): array
    {
        try {
            $allTemplates = [];
            $statuses = ['APPROVED', 'PENDING', 'REJECTED', 'PAUSED', 'DISABLED'];

            foreach ($statuses as $status) {
                $url = "{$this->baseUrl}/{$this->getBusinessAccountId()}/message_templates";
                $params = [
                    'limit' => 100,
                    'status' => $status,
                    'fields' => 'id,name,status,category,language,components,quality_score,rejected_reason',
                ];

                do {
                    $response = Http::withToken($this->getToken())
                        ->timeout(15)
                        ->get($url, $params);

                    if (!$response->successful()) {
                        Log::error('WhatsApp get templates failed', ['status_filter' => $status, 'response' => $response->json()]);
                        break;
                    }

                    $data = $response->json('data', []);
                    $allTemplates = array_merge($allTemplates, $data);

                    $url = $response->json('paging.next');
                    $params = [];
                } while ($url);
            }

            // Deduplicate by template id
            $unique = collect($allTemplates)->unique('id')->values()->toArray();

            return $unique;
        } catch (\Exception $e) {
            Log::error('WhatsApp get templates failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync templates from Meta API into local database.
     */
    public function syncTemplates(): int
    {
        $metaTemplates = $this->getTemplates();
        $synced = 0;

        foreach ($metaTemplates as $template) {
            $bodyComponent = collect($template['components'] ?? [])->firstWhere('type', 'BODY');
            $body = $bodyComponent['text'] ?? '';
            $variables = [];

            if ($body) {
                preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);
                $variables = array_values(array_unique($matches[1]));
            }

            $qualityScore = $template['quality_score']['score'] ?? null;
            $rejectedReason = $template['rejected_reason'] ?? null;

            WhatsAppTemplate::updateOrCreate(
                ['template_name' => $template['name']],
                [
                    'meta_template_id' => $template['id'],
                    'body' => $body,
                    'variables' => $variables,
                    'language_code' => $template['language'] ?? 'en',
                    'category' => $template['category'] ?? 'UTILITY',
                    'components' => $template['components'] ?? [],
                    'status' => $template['status'] ?? 'PENDING',
                    'quality_score' => $qualityScore,
                    'rejected_reason' => $rejectedReason !== 'NONE' ? $rejectedReason : null,
                ]
            );
            $synced++;
        }

        // Remove local templates that no longer exist on Meta
        $metaNames = collect($metaTemplates)->pluck('name')->toArray();
        WhatsAppTemplate::whereNotIn('template_name', $metaNames)->delete();

        return $synced;
    }

    /**
     * Create a new message template on Meta.
     */
    public function createMetaTemplate(string $name, string $category, string $language, array $components): array
    {
        try {
            $response = Http::withToken($this->getToken())
                ->timeout(15)
                ->post("{$this->baseUrl}/{$this->getBusinessAccountId()}/message_templates", [
                    'name' => $name,
                    'category' => $category,
                    'allow_category_change' => true,
                    'language' => $language,
                    'components' => $components,
                ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp create template failed: ' . $e->getMessage());
            return ['success' => false, 'data' => ['error' => ['message' => $e->getMessage()]]];
        }
    }

    /**
     * Edit an existing message template on Meta.
     */
    public function editMetaTemplate(string $metaTemplateId, array $components): array
    {
        try {
            $response = Http::withToken($this->getToken())
                ->timeout(15)
                ->post("{$this->baseUrl}/{$metaTemplateId}", [
                    'components' => $components,
                ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp edit template failed: ' . $e->getMessage());
            return ['success' => false, 'data' => ['error' => ['message' => $e->getMessage()]]];
        }
    }

    /**
     * Delete a message template from Meta.
     */
    public function deleteMetaTemplate(string $templateName): array
    {
        try {
            $response = Http::withToken($this->getToken())
                ->timeout(15)
                ->delete("{$this->baseUrl}/{$this->getBusinessAccountId()}/message_templates", [
                    'name' => $templateName,
                ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp delete template failed: ' . $e->getMessage());
            return ['success' => false, 'data' => ['error' => ['message' => $e->getMessage()]]];
        }
    }

    public function sendTextMessage(string $to, string $text): ?WhatsAppMessage
    {
        if (empty($this->getToken())) {
            Log::warning('WhatsApp token missing. Bypassing sendTextMessage.', ['to' => $to]);
            return null;
        }

        $url = "{$this->baseUrl}/{$this->getPhoneNumberId()}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $text],
        ];

        try {
            Log::info('WhatsApp sending text message', ['to' => $to, 'text' => $text]);

            $response = Http::withToken($this->getToken())
                ->timeout(10)
                ->post($url, $payload);

            Log::info('WhatsApp text message response', ['status' => $response->status(), 'body' => $response->json()]);

            $waMessageId = $response->json('messages.0.id');

            return WhatsAppMessage::create([
                'wa_message_id' => $waMessageId,
                'phone_number' => $to,
                'direction' => 'outgoing',
                'type' => 'text',
                'body' => $text,
                'status' => $response->successful() ? 'sent' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function sendTemplateMessage(string $to, string $templateName, string $languageCode = 'en_US', array $components = []): ?WhatsAppMessage
    {
        if (empty($this->getToken())) {
            Log::warning('WhatsApp token missing. Bypassing sendTemplateMessage.', ['to' => $to, 'template' => $templateName]);
            return null;
        }

        $url = "{$this->baseUrl}/{$this->getPhoneNumberId()}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $languageCode],
                'components' => $components,
            ],
        ];

        try {
            Log::info('WhatsApp sending template message', ['to' => $to, 'template' => $templateName, 'payload' => $payload]);

            $response = Http::withToken($this->getToken())
                ->timeout(10)
                ->post($url, $payload);

            Log::info('WhatsApp template message response', ['to' => $to, 'template' => $templateName, 'status' => $response->status(), 'body' => $response->json()]);

            $waMessageId = $response->json('messages.0.id');

            return WhatsAppMessage::create([
                'wa_message_id' => $waMessageId,
                'phone_number' => $to,
                'direction' => 'outgoing',
                'type' => 'template',
                'body' => "Template: {$templateName}",
                'template_name' => $templateName,
                'status' => $response->successful() ? 'sent' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp template send failed', ['to' => $to, 'template' => $templateName, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function sendMediaMessage(string $to, string $name, string $pdfLink, string $pdfName): ?WhatsAppMessage
    {
        if (empty($this->getToken())) {
            Log::warning('WhatsApp token missing. Bypassing sendMediaMessage.', ['to' => $to, 'name' => $name]);
            return null;
        }

        $url = "{$this->baseUrl}/{$this->getPhoneNumberId()}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => '91' . $to,
            'type' => 'template',
            'template' => [
                'name' => 'estimate_requested',
                'language' => ['code' => 'ta'],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'document',
                                'document' => [
                                    'link' => $pdfLink,
                                    'filename' => $pdfName,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $name],
                        ],
                    ],
                ],
            ],
        ];

        try {
            Log::info('WhatsApp sending estimate (media) message', ['to' => $to, 'name' => $name, 'pdfLink' => $pdfLink, 'payload' => $payload]);

            $response = Http::withToken($this->getToken())
                ->timeout(10)
                ->post($url, $payload);

            Log::info('WhatsApp estimate message response', ['to' => $to, 'status' => $response->status(), 'body' => $response->json()]);

            $waMessageId = $response->json('messages.0.id');

            return WhatsAppMessage::create([
                'wa_message_id' => $waMessageId,
                'phone_number' => '91' . $to,
                'direction' => 'outgoing',
                'type' => 'document',
                'body' => "Document: {$pdfName}",
                'template_name' => 'estimate_requested',
                'media_url' => $pdfLink,
                'status' => $response->successful() ? 'sent' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp media send failed', ['to' => $to, 'name' => $name, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Always compress an image before sending via WhatsApp.
     */
    protected function compressImage(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        try {
            $image = imagecreatefromstring(file_get_contents($filePath));
            if ($image) {
                $originalSize = filesize($filePath);
                imagejpeg($image, $filePath, 60);
                imagedestroy($image);
                Log::info('WhatsApp image compressed', ['path' => $filePath, 'original_size' => $originalSize, 'compressed_size' => filesize($filePath)]);
            }
        } catch (\Exception $e) {
            Log::warning('WhatsApp image compression failed', ['error' => $e->getMessage()]);
        }
    }

    public function sendOrderDispatchMessage(string $to, string $name, string $lrCopyLink, ?string $localFilePath = null): ?WhatsAppMessage
    {
        if (empty($this->getToken())) {
            Log::warning('WhatsApp token missing. Bypassing sendOrderDispatchMessage.', ['to' => $to, 'name' => $name]);
            return null;
        }

        $url = "{$this->baseUrl}/{$this->getPhoneNumberId()}/messages";

        // Always compress before sending
        if ($localFilePath) {
            $this->compressImage($localFilePath);
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => '91' . $to,
            'type' => 'template',
            'template' => [
                'name' => 'lr_copy',
                'language' => ['code' => 'ta'],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            ['type' => 'image', 'image' => ['link' => $lrCopyLink]],
                        ],
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'parameter_name' => 'name', 'text' => $name],
                        ],
                    ],
                ],
            ],
        ];

        try {
            Log::info('WhatsApp sending dispatch (LR copy) message', ['to' => $to, 'name' => $name, 'lrCopyLink' => $lrCopyLink, 'payload' => $payload]);

            $response = Http::withToken($this->getToken())
                ->timeout(10)
                ->post($url, $payload);

            Log::info('WhatsApp dispatch message response', ['to' => $to, 'status' => $response->status(), 'body' => $response->json()]);

            $waMessageId = $response->json('messages.0.id');

            return WhatsAppMessage::create([
                'wa_message_id' => $waMessageId,
                'phone_number' => '91' . $to,
                'direction' => 'outgoing',
                'type' => 'image',
                'body' => "LR Copy sent to {$name}",
                'template_name' => 'lr_copy',
                'media_url' => $lrCopyLink,
                'status' => $response->successful() ? 'sent' : 'failed',
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp dispatch message failed', ['to' => $to, 'name' => $name, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
