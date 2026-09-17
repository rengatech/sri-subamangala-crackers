<?php

namespace App\Filament\Resources\AllOrdersResource\Pages;

use App\Filament\Resources\AllOrdersResource;
use App\Filament\Resources\AllOrdersResource\Widgets\AllOrdersOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllOrders extends ListRecords
{
    protected static string $resource = AllOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return "All Orders"; 
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AllOrdersOverview::class,
        ];
    }
}
