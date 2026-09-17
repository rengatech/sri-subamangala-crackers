<?php

namespace App\Filament\Resources\DeletedCategoryResource\Pages;

use App\Filament\Resources\DeletedCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeletedCategory extends CreateRecord
{
    protected static string $resource = DeletedCategoryResource::class;
}
