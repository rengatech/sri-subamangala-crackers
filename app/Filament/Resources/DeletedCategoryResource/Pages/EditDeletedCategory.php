<?php

namespace App\Filament\Resources\DeletedCategoryResource\Pages;

use App\Filament\Resources\DeletedCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeletedCategory extends EditRecord
{
    protected static string $resource = DeletedCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
