<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AllProductResource\Pages;
use App\Models\Product;
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class AllProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Products';

    protected static ?string $navigationLabel = 'AllProduct';

    protected static ?int $navigationSort = 2;

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
                ->reactive()
                ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                    $set('url_slug', str::slug($state));
                }),

            Forms\Components\TextInput::make('url_slug')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('seo_title')
                ->required()
                ->maxLength(255),

            // Forms\Components\TextInput::make('description')
            // ->required()
            // ->maxLength(255),

            RichEditor::make('description')
                ->required(),

            Select::make('category_id')
                ->label('category')
                ->relationship('category', 'category')
                ->required()
                ->preload()
                ->searchable(),

            Forms\Components\TextInput::make('price')
                ->required()
                ->maxLength(255),

            FileUpload::make('image')->image()
                ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tamil_name'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url_slug'),
                Tables\Columns\TextColumn::make('seo_title'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('category.category'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\ImageColumn::make('image'),

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
            'index' => Pages\ListAllProducts::route('/'),
            'create' => Pages\CreateAllProduct::route('/create'),
            'edit' => Pages\EditAllProduct::route('/{record}/edit'),
        ];
    }
}
