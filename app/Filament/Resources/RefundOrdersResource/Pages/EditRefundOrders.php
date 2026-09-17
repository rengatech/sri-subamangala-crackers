<?php

namespace App\Filament\Resources\RefundOrdersResource\Pages;

use App\Filament\Resources\RefundOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefundOrders extends EditRecord
{
    protected static string $resource = RefundOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
