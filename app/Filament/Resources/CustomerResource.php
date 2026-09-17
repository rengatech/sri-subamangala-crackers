<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CustomerResource extends Resource
{

    protected static ?string $model = Customer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?string $navigationLabel = 'Customer';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Customer Details')
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
                    ])->columns(2),

                Section::make('Order Details')
                    ->schema([
                        Placeholder::make('order_id')
                            ->label('Order ID')
                            ->content(fn (?Customer $record) => $record?->orders?->first()?->id ?? '-'),
                        Placeholder::make('order_status')
                            ->label('Status')
                            ->content(fn (?Customer $record) => $record?->orders?->first()?->status ?? '-'),
                        Placeholder::make('order_total')
                            ->label('Net Total')
                            ->content(fn (?Customer $record) => $record?->orders?->first() ? '₹' . $record->orders->first()->net_total : '-'),
                        Placeholder::make('order_date')
                            ->label('Ordered At')
                            ->content(fn (?Customer $record) => $record?->orders?->first()?->created_at?->format('d-m-Y H:i') ?? '-'),
                    ])->columns(4)
                    ->visible(fn (?Customer $record) => $record?->orders?->first() !== null),

                Section::make('Delivery Address')
                    ->schema([
                        Placeholder::make('address')
                            ->label('Address')
                            ->content(fn (?Customer $record) => $record?->orders?->first()?->address?->address ?? '-'),
                        Placeholder::make('city_town')
                            ->label('City/Town')
                            ->content(fn (?Customer $record) => $record?->orders?->first()?->address?->city_town ?? '-'),
                    ])->columns(2)
                    ->visible(fn (?Customer $record) => $record?->orders?->first()?->address !== null),

                Section::make('Order Items')
                    ->schema([
                        Placeholder::make('items_list')
                            ->label('')
                            ->content(function (?Customer $record) {
                                $order = $record?->orders?->first();
                                if (!$order) return '-';

                                $items = $order->items()->with('product')->get();
                                if ($items->isEmpty()) return 'No items';

                                $rows = $items->map(function ($item, $index) {
                                    $name = $item->product?->name ?? 'N/A';
                                    $qty = $item->quantity;
                                    $total = $item->total;
                                    $sno = $index + 1;
                                    return "<tr><td style='padding:4px 8px;border-bottom:1px solid #e5e7eb;'>{$sno}</td><td style='padding:4px 8px;border-bottom:1px solid #e5e7eb;'>{$name}</td><td style='padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:center;'>{$qty}</td><td style='padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:right;'>₹{$total}</td></tr>";
                                })->implode('');

                                return new HtmlString("
                                    <table style='width:100%;border-collapse:collapse;font-size:0.875rem;'>
                                        <thead><tr style='background:#f9fafb;'>
                                            <th style='padding:6px 8px;text-align:left;border-bottom:2px solid #e5e7eb;'>S.No</th>
                                            <th style='padding:6px 8px;text-align:left;border-bottom:2px solid #e5e7eb;'>Product</th>
                                            <th style='padding:6px 8px;text-align:center;border-bottom:2px solid #e5e7eb;'>Qty</th>
                                            <th style='padding:6px 8px;text-align:right;border-bottom:2px solid #e5e7eb;'>Total</th>
                                        </tr></thead>
                                        <tbody>{$rows}</tbody>
                                    </table>
                                ");
                            }),
                    ])
                    ->visible(fn (?Customer $record) => $record?->orders?->first() !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(['name', 'mobile_number']),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('mobile_number'),
                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp Number')
                    ->url(fn (Customer $record): ?string => $record->whatsapp_number ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->whatsapp_number) : null)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->tooltip('Open WhatsApp')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
                Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->url(fn (Customer $record): ?string => $record->whatsapp_number ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->whatsapp_number) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Customer $record): bool => !empty($record->whatsapp_number)),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                Actions\RestoreBulkAction::make(),
                SendWhatsAppBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            // 'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
