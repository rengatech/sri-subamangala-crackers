<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Tables;
use App\Models\Product;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn($query) => $query->withTrashed(),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::recalculateTotal($get, $set);
                    }),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::recalculateTotal($get, $set);
                    }),

                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->required()
                    ->readOnly(),
            ]);
    }

    protected static function recalculateTotal(Get $get, Set $set): void
    {
        $product = Product::withTrashed()->with('category')->find($get('product_id'));
        $quantity = (int) $get('quantity');

        if (!$product) {
            return;
        }

        $price = $product->price;

        $hasDiscount = true;
        if ($product->category && isset($product->category->has_discount)) {
            $hasDiscount = $product->category->has_discount;
        }

        if ($hasDiscount) {
            $globalDiscount = app(\App\Settings\GeneralSettings::class)->global_discount ?? 0;
            $discountAmount = round(($price * $globalDiscount) / 100);
            $price = $price - $discountAmount;
        }

        $set('total', (int) round($price * $quantity));
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('total'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }
}