<?php

namespace App\Filament\Resources\DeletedCategoryResource\Pages;

use App\Filament\Resources\DeletedCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeletedCategories extends ListRecords
{
    protected static string $resource = DeletedCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
