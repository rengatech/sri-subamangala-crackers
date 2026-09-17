<?php

namespace App\Filament\Actions;

use App\Models\WhatsAppTemplate;
use Filament\Actions;
use App\Services\WhatsAppApiService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class SendWhatsAppBulkAction
{
    public static function make(string $name = 'Send WhatsApp'): BulkAction
    {
        return BulkAction::make($name)
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('success')
            ->form([
                Forms\Components\Select::make('send_type')
                    ->label('Message Type')
                    ->options([
                        'text' => 'Custom Text Message',
                        'template' => 'WhatsApp Template',
                    ])
                    ->default('text')
                    ->required()
                    ->live(),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->rows(4)
                    ->required()
                    ->visible(fn (Forms\Get $get) => $get('send_type') === 'text')
                    ->helperText('This message will be sent to all selected customers via WhatsApp Cloud API.'),

                Forms\Components\Select::make('template_name')
                    ->label('Template')
                    ->options(fn () => self::getTemplateOptions())
                    ->required()
                    ->visible(fn (Forms\Get $get) => $get('send_type') === 'template')
                    ->helperText('Only APPROVED templates can be sent.'),
            ])
            ->action(function (Collection $records, array $data) {
                $service = app(WhatsAppApiService::class);
                $sent = 0;
                $failed = 0;
                $skipped = 0;

                foreach ($records as $record) {
                    $phone = self::extractPhone($record);

                    if (!$phone) {
                        $skipped++;
                        continue;
                    }

                    $result = null;

                    if ($data['send_type'] === 'text') {
                        $result = $service->sendTextMessage($phone, $data['message']);
                    } else {
                        $result = $service->sendTemplateMessage($phone, $data['template_name']);
                    }

                    $result ? $sent++ : $failed++;
                }

                Notification::make()
                    ->title("WhatsApp: {$sent} sent, {$failed} failed, {$skipped} skipped (no number)")
                    ->color($failed > 0 ? 'warning' : 'success')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->send();
            })
            ->deselectRecordsAfterCompletion()
            ->requiresConfirmation()
            ->modalHeading('Send WhatsApp Message')
            ->modalDescription('Send a message to all selected records via WhatsApp Cloud API.');
    }

    protected static function extractPhone($record): ?string
    {
        // Order-based resources: get phone from customer
        if (method_exists($record, 'customer') && $record->customer) {
            $number = $record->customer->whatsapp_number ?? $record->customer->mobile_number ?? null;
        }
        // Customer resource: get phone directly
        elseif (isset($record->whatsapp_number)) {
            $number = $record->whatsapp_number;
        }
        // Dispatch resource: get from order->customer
        elseif (method_exists($record, 'order') && $record->order?->customer) {
            $number = $record->order->customer->whatsapp_number ?? $record->order->customer->mobile_number ?? null;
        } else {
            return null;
        }

        if (!$number) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $number);

        // Add country code if not present
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }

        return $phone;
    }

    protected static function getTemplateOptions(): array
    {
        return WhatsAppTemplate::where('status', WhatsAppTemplate::STATUS_APPROVED)
            ->pluck('template_name', 'template_name')
            ->toArray();
    }
}
