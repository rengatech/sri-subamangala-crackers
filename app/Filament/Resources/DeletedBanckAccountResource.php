<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeletedBanckAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class DeletedBanckAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-rupee';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin';

    protected static ?string $navigationLabel = 'Deleted BankAccounts';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::onlyTrashed()->count();
    }
    
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('bank_name')
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('branch')
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('account_number')
                ->required(),

                Forms\Components\TextInput::make('ifsc_code')
                ->required(),

                Forms\Components\TextInput::make('upi_id')
                ->required(),

                Forms\Components\TextInput::make('g_pay')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(function () {

            $query = BankAccount::query();

            return $query->onlyTrashed();
        })
            ->columns([
                Tables\Columns\TextColumn::make('bank_name'),
                Tables\Columns\TextColumn::make('branch'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('account_number'),
                Tables\Columns\TextColumn::make('ifsc_code'),
                Tables\Columns\TextColumn::make('upi_id'),
                Tables\Columns\TextColumn::make('g_pay'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListDeletedBanckAccounts::route('/'),
            'create' => Pages\CreateDeletedBanckAccount::route('/create'),
            'edit' => Pages\EditDeletedBanckAccount::route('/{record}/edit'),
        ];
    }
}
