<?php

namespace App\Filament\Resources\PackingOrderResource\Pages;

use App\Filament\Resources\PackingOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackingOrders extends ListRecords
{
    protected static string $resource = PackingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return "Packing Orders";
    }
}
