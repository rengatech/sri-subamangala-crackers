<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeletedProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeletedProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Products';

    protected static ?string $navigationLabel = 'DeletedProducts';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::onlyTrashed()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('tamil_name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->reactive(),

            Select::make('category_id')
                ->label('category')
                ->relationship('category', 'category')
                ->required()
                ->preload()
                ->searchable(),

            Forms\Components\TextInput::make('price')
                ->required()
                ->maxLength(255),

            FileUpload::make('image')
            ->image()
            ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

        ->query(function () {

            $query = Product::query();

            return $query->onlyTrashed();
        })

            ->columns([
               Tables\Columns\TextColumn::make('tamil_name'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('category.category'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\ImageColumn::make('image'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\RestoreAction::make()->button(),
                Actions\EditAction::make()->button(),
            ])
            ->bulkActions([
                Actions\RestoreBulkAction::make(),
                Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListDeletedProducts::route('/'),
            'create' => Pages\CreateDeletedProduct::route('/create'),
            'edit' => Pages\EditDeletedProduct::route('/{record}/edit'),
        ];
    }
}
