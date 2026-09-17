<?php

namespace App\Filament\Resources\AllCustomerResource\Pages;

use App\Filament\Resources\AllCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllCustomers extends ListRecords
{
    protected static string $resource = AllCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
