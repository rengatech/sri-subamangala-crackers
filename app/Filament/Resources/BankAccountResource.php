<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-rupee';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin';

    protected static ?string $navigationLabel = 'BankAccount';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)
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

                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->imagePreviewHeight('150')
                    ->directory('bank-account-images')
                    ->disk('public')
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes(['image/*'])
                    ->maxSize(10240)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bank_name'),
                Tables\Columns\TextColumn::make('branch'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('account_number'),
                Tables\Columns\TextColumn::make('ifsc_code'),
                Tables\Columns\TextColumn::make('upi_id'),
                Tables\Columns\TextColumn::make('g_pay'),
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->height(60),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                // Actions\RestoreAction::make(),

            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                Actions\RestoreBulkAction::make(),

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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
