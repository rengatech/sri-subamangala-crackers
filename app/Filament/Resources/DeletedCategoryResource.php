<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeletedCategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeletedCategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Deleted Categories';
    protected static string|\UnitEnum|null $navigationGroup = 'Categories';
    protected static ?int $navigationSort = 3;


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::onlyTrashed()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('category')
                ->required()
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(function () {

            $query = Category::query();

            return $query->onlyTrashed();
        })
            ->columns([
                TextColumn::make('category')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Actions\EditAction::make(),
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
            'index' => Pages\ListDeletedCategories::route('/'),
            'create' => Pages\CreateDeletedCategory::route('/create'),
            'edit' => Pages\EditDeletedCategory::route('/{record}/edit'),
        ];
    }
}
