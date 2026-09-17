<?php

namespace App\Filament\Resources\DeletedProductResource\Pages;

use App\Filament\Resources\DeletedProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeletedProducts extends ListRecords
{
    protected static string $resource = DeletedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
