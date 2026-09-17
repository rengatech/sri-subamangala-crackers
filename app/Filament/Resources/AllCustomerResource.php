<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AllCustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class AllCustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?string $navigationLabel = 'All Customers';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
               Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile_number')
                    ->required(),
                Forms\Components\TextInput::make('whatsapp_number')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(['name', 'mobile_number']),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('mobile_number'),
                Tables\Columns\TextColumn::make('whatsapp_number')
                    ->label('WhatsApp Number'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAllCustomers::route('/'),
            'create' => Pages\CreateAllCustomer::route('/create'),
            'edit' => Pages\EditAllCustomer::route('/{record}/edit'),
        ];
    }
}
