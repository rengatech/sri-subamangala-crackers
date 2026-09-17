<?php

namespace App\Filament\Resources\CancelOrderResource\Pages;

use App\Filament\Resources\CancelOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCancelOrder extends EditRecord
{
    protected static string $resource = CancelOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
