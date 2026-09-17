<?php

namespace App\Filament\Resources\DeletedBanckAccountResource\Pages;

use App\Filament\Resources\DeletedBanckAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeletedBanckAccounts extends ListRecords
{
    protected static string $resource = DeletedBanckAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
