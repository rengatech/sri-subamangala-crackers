<?php

namespace App\Filament\Resources\Billings\Pages;

use App\Filament\Resources\Billings\BillingResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
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
                ->url(fn ($record) => route('admin.billings.print', $record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    // Runs after the items are saved: recalculates totals + fills product_name
    protected function afterSave(): void
    {
        $this->record->recalculate();
        $this->refreshFormData(['sub_total', 'net_amount']);
    }
}