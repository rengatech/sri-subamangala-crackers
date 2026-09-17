<?php

namespace App\Filament\Resources\DeletedOrdersResource\Pages;

use App\Filament\Resources\DeletedOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeletedOrders extends EditRecord
{
    protected static string $resource = DeletedOrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
