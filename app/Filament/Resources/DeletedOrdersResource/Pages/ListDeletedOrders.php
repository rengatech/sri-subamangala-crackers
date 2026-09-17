<?php

namespace App\Filament\Resources\DeletedOrdersResource\Pages;

use App\Filament\Resources\DeletedOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeletedOrders extends ListRecords
{
    protected static string $resource = DeletedOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return "Deleted Orders";
    }
}
