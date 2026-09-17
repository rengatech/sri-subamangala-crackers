<?php

namespace App\Filament\Resources\Billings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Product;
use Filament\Schemas\Schema;

class BillingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_name')->required(),
                TextInput::make('mobile_number'),
                Textarea::make('address')
                    ->columnSpanFull(),

                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->options(Product::query()->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($product = Product::find($state)) {
                                    $set('product_name', $product->name);
                                    $set('price', $product->price ?? 0);
                                    $qty = (int) ($get('quantity') ?: 1);
                                    $set('total', $product->price * $qty);
                                }
                            })
                            ->required(),
                        TextInput::make('product_name')->hidden(),
                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(function ($state, $get, $set) {
                                $price = (float) ($get('price') ?: 0);
                                $qty = (int) ($state ?: 1);
                                $set('total', $price * $qty);
                            })
                            ->required(),
                        TextInput::make('price')
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($state, $get, $set) {
                                $qty = (int) ($get('quantity') ?: 1);
                                $price = (float) ($state ?: 0);
                                $set('total', $price * $qty);
                            })
                            ->required(),
                        TextInput::make('total')
                            ->numeric()
                            ->readonly()
                            ->required(),
                    ])
                    ->live()
                    ->afterStateUpdated(function ($get, $set) {
                        self::updateTotals($get, $set);
                    })
                    ->columnSpanFull()
                    ->columns(4),

                TextInput::make('sub_total')
                    ->required()
                    ->numeric()
                    ->readonly()
                    ->default(0.0),
                TextInput::make('discount')
                    ->label('Discount')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($get, $set) {
                        self::updateTotals($get, $set);
                    }),
                TextInput::make('net_amount')
                    ->required()
                    ->numeric()
                    ->readonly()
                    ->default(0.0),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
            ]);
    }

    public static function updateTotals($get, $set): void
    {
        $items = $get('items') ?? [];
        $subTotal = collect($items)->sum(fn($item) => (float) ($item['total'] ?? 0));
        $set('sub_total', $subTotal);

        $discount = (float) ($get('discount') ?: 0);
        $set('net_amount', max(0, $subTotal - $discount));
    }
}
