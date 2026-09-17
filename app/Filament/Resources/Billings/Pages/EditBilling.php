<?php

namespace App\Filament\Resources\Billings\Pages;

use App\Filament\Resources\Billings\BillingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditBilling extends EditRecord
{
    protected static string $resource = BillingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print Bill')
                ->icon('heroicon-o-printer')
                ->url(fn($record) => route('admin.billings.print', $record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
