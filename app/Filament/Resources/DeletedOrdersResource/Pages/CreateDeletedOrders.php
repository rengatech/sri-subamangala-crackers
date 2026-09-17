<?php

namespace App\Filament\Resources\DeletedOrdersResource\Pages;

use App\Filament\Resources\DeletedOrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeletedOrders extends CreateRecord
{
    protected static string $resource = DeletedOrdersResource::class;
}
