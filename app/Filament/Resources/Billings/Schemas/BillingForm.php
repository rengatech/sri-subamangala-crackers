<?php

namespace App\Filament\Resources\Billings\Schemas;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                if ($product = Product::find($state)) {
                                    $qty = (int) ($get('quantity') ?: 1);
                                    $price = (float) ($product->price ?? 0);

                                    $set('product_name', $product->name);
                                    $set('price', $price);
                                    $set('total', round($price * $qty, 2));
                                }
                                self::updateTotals($get, $set, '../../');
                            })
                            ->required(),

                        // Hidden::make (not TextInput->hidden()) so product_name is actually saved
                        Hidden::make('product_name'),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $price = (float) ($get('price') ?: 0);
                                $qty = (int) ($state ?: 1);
                                $set('total', round($price * $qty, 2));
                                self::updateTotals($get, $set, '../../');
                            })
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $qty = (int) ($get('quantity') ?: 1);
                                $price = (float) ($state ?: 0);
                                $set('total', round($price * $qty, 2));
                                self::updateTotals($get, $set, '../../');
                            })
                            ->required(),

                        TextInput::make('total')
                            ->numeric()
                            ->readOnly()
                            ->required(),
                    ])
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                    })
                    ->deleteAction(
                        fn (Action $action) => $action->after(
                            fn (Get $get, Set $set) => self::updateTotals($get, $set)
                        )
                    )
                    ->columnSpanFull()
                    ->columns(4),

                TextInput::make('sub_total')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->default(0.0),

                TextInput::make('discount')
                    ->label('Discount (%)')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                    }),

                TextInput::make('net_amount')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->default(0.0),

                TextInput::make('status')
                    ->required()
                    ->default('pending'),
            ]);
    }

    /**
     * $prefix is '../../' when called from inside a repeater item, '' otherwise.
     * Totals are computed from quantity * price, so they never go out of sync.
     */
    public static function updateTotals(Get $get, Set $set, string $prefix = ''): void
    {
        $items = $get($prefix . 'items') ?? [];

        $subTotal = collect($items)->sum(
            fn ($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['price'] ?? 0)
        );

        $discountPercent = (float) ($get($prefix . 'discount') ?: 0);
        $discountAmount = $subTotal * ($discountPercent / 100);

        $set($prefix . 'sub_total', round($subTotal, 2));
        $set($prefix . 'net_amount', round(max(0, $subTotal - $discountAmount), 2));
    }
}