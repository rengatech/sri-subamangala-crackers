<?php

namespace App\Filament\Resources\RefundOrdersResource\Pages;

use App\Filament\Resources\RefundOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRefundOrders extends ListRecords
{
    protected static string $resource = RefundOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return "Refund Orders";
    }
}
