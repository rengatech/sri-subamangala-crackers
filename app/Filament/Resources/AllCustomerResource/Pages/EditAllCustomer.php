<?php

namespace App\Filament\Resources\AllCustomerResource\Pages;

use App\Filament\Resources\AllCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllCustomer extends EditRecord
{
    protected static string $resource = AllCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
