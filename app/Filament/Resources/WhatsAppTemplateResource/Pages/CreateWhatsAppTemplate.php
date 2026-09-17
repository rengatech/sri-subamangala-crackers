<?php

namespace App\Filament\Resources\WhatsAppTemplateResource\Pages;

use App\Filament\Resources\WhatsAppTemplateResource;
use App\Services\WhatsAppApiService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateWhatsAppTemplate extends CreateRecord
{
    protected static string $resource = WhatsAppTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Build Meta API components from form data
        $components = [];

        if (!empty($data['header_text'])) {
            $components[] = [
                'type' => 'HEADER',
                'format' => 'TEXT',
                'text' => $data['header_text'],
            ];
        }

        $components[] = [
            'type' => 'BODY',
            'text' => $data['body'],
        ];

        if (!empty($data['footer_text'])) {
            $components[] = [
                'type' => 'FOOTER',
                'text' => $data['footer_text'],
            ];
        }

        // Create on Meta first
        $service = app(WhatsAppApiService::class);
        $result = $service->createMetaTemplate(
            $data['template_name'],
            $data['category'],
            $data['language_code'],
            $components
        );

        if (!$result['success']) {
            $error = $result['data']['error']['message'] ?? 'Unknown error';

            Notification::make()
                ->danger()
                ->title('Meta API Error')
                ->body($error)
                ->persistent()
                ->send();

            $this->halt();
        }

        // Parse variables from body
        $variables = [];
        if (preg_match_all('/\{\{(\d+)\}\}/', $data['body'], $matches)) {
            $variables = array_values(array_unique($matches[1]));
        }

        $data['meta_template_id'] = $result['data']['id'] ?? null;
        $data['variables'] = $variables;
        $data['components'] = $components;
        $data['status'] = 'PENDING';

        // Remove non-model fields
        unset($data['header_text'], $data['footer_text']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
