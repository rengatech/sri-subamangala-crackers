<?php

namespace App\Filament\Resources\CancelOrderResource\Pages;

use App\Filament\Resources\CancelOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCancelOrders extends ListRecords
{
    protected static string $resource = CancelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return "Cancel Orders";
    }
}
