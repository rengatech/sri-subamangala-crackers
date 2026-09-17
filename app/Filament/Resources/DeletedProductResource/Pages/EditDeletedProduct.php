<?php

namespace App\Filament\Resources\DeletedProductResource\Pages;

use App\Filament\Resources\DeletedProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeletedProduct extends EditRecord
{
    protected static string $resource = DeletedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
