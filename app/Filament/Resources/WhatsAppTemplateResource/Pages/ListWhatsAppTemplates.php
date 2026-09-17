<?php

namespace App\Filament\Resources\WhatsAppTemplateResource\Pages;

use App\Filament\Resources\WhatsAppTemplateResource;
use Filament\Actions;
use App\Services\WhatsAppApiService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppTemplates extends ListRecords
{
    protected static string $resource = WhatsAppTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sync from Meta')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function (): void {
                    $service = app(WhatsAppApiService::class);
                    $count = $service->syncTemplates();

                    Notification::make()
                        ->success()
                        ->title('Templates Synced')
                        ->body("Synced {$count} templates from Meta.")
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }

}
