<?php

namespace App\Filament\Resources\ConfirmOrderResource\Pages;

use App\Filament\Resources\ConfirmOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfirmOrders extends ListRecords
{
    protected static string $resource = ConfirmOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return "Confirm Orders";
    }
}
