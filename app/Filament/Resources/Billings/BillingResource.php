<?php

namespace App\Filament\Resources\Billings;

use App\Filament\Resources\Billings\Pages\CreateBilling;
use App\Filament\Resources\Billings\Pages\EditBilling;
use App\Filament\Resources\Billings\Pages\ListBillings;
use App\Filament\Resources\Billings\Schemas\BillingForm;
use App\Filament\Resources\Billings\Tables\BillingsTable;
use App\Models\Billing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BillingResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Billing';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'customer_name';

    public static function form(Schema $schema): Schema
    {
        return BillingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBillings::route('/'),
            'create' => CreateBilling::route('/create'),
            'edit' => EditBilling::route('/{record}/edit'),
        ];
    }
}
