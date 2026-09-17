<?php

namespace App\Filament\Resources\WhatsAppTemplateResource\Pages;

use App\Filament\Resources\WhatsAppTemplateResource;
use App\Services\WhatsAppApiService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppTemplate extends EditRecord
{
    protected static string $resource = WhatsAppTemplateResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Extract header and footer from components for the form
        $components = $data['components'] ?? [];

        $header = collect($components)->firstWhere('type', 'HEADER');
        $footer = collect($components)->firstWhere('type', 'FOOTER');

        $data['header_text'] = $header['text'] ?? '';
        $data['footer_text'] = $footer['text'] ?? '';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

        // Update on Meta
        $record = $this->record;
        if ($record->meta_template_id) {
            $service = app(WhatsAppApiService::class);
            $result = $service->editMetaTemplate($record->meta_template_id, $components);

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
        }

        // Parse variables
        $variables = [];
        if (preg_match_all('/\{\{(\d+)\}\}/', $data['body'], $matches)) {
            $variables = array_values(array_unique($matches[1]));
        }

        $data['variables'] = $variables;
        $data['components'] = $components;

        unset($data['header_text'], $data['footer_text']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
