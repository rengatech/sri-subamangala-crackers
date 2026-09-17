<?php

namespace App\Filament\Resources\AllCategoryResource\Pages;

use App\Filament\Resources\AllCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllCategory extends EditRecord
{
    protected static string $resource = AllCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
