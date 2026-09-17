<?php

namespace App\Filament\Resources\DeletedProductResource\Pages;

use App\Filament\Resources\DeletedProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeletedProduct extends CreateRecord
{
    protected static string $resource = DeletedProductResource::class;
}
