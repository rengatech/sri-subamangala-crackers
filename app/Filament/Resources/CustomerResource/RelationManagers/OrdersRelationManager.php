<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Orders';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_total')
                    ->label('Net Total')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'placed' => 'warning',
                        'payment_received', 'confirmed', 'packing', 'dispatched' => 'success',
                        'cancelled' => 'danger',
                        'refund' => 'gray',
                        default => 'primary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('address.city_town')
                    ->label('City/Town'),
                Tables\Columns\TextColumn::make('address.address')
                    ->label('Address')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->address?->address),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ordered At')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\Action::make('download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->button()
                    ->color('info')
                    ->url(fn ($record) => route('admin.orders.download', ['id' => $record->id]))
                    ->openUrlInNewTab(),
                Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->button()
                    ->url(fn ($record) => route('filament.admin.resources.orders.edit', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
