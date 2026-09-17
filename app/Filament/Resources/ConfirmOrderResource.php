<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\ConfirmOrderResource\Pages;
use App\Filament\Resources\ConfirmOrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConfirmOrderResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Confirm Orders';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'payment_received')->whereYear('created_at', static::getSelectedYear())->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Order Summary')
                ->schema([

                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(Customer::all()?->pluck('name', 'id')?->filter()?->toArray() + [null => 'No customer found'])
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('net_total')
                        ->required()
                        ->maxLength(255),

                        Select::make('address_id')
                        ->label('Address')
                        ->options(Address::all()?->pluck('address', 'id')?->filter()?->toArray() + [null => 'No address found'])
                        ->required()
                        ->searchable(),

                    Select::make('city_town')
                        ->label('City/Town')
                        ->options(Address::all()?->pluck('city_town', 'city_town')?->filter()?->toArray() + [null => 'No city found'])
                        ->searchable(),

                    Forms\Components\Select::make('status')
                            ->options([
                                'placed' => 'placed',
                                'payment_received' => 'payment_received',
                                'cancelled' => 'cancelled',
                                'packing' => 'packing',
                            ]),

                ])->columns()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

        ->query(function () {

            $query = Order::query();

            return $query->with(['customer', 'address'])->where('status', 'payment_received')->whereYear('created_at', session('admin_selected_year', now()->year))->latest('created_at');
        })

            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order ID')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('customer.mobile_number')->label('Mobile number')->searchable()->toggleable()->sortable(),
                // Tables\Columns\TextColumn::make('address.address')->toggleable(),
                Tables\Columns\TextColumn::make('address.city_town')->label('City/Town')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('net_total')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d-m-y H:i:s')
                ->sortable()
                ->toggleable(),
            ])
            ->filters([
                Filter::make('Created_at')
                ->form([
                    DatePicker::make('From_date'),
                    DatePicker::make('To_date'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['From_date'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['To_date'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
            ])
            ->actions([
                Actions\Action::make('download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->button()
                    ->color('info')
                    ->url(
                        fn (Order $record): string => route('admin.orders.download', ['id' => $record->id]),
                        shouldOpenInNewTab: true
                    ),

                Actions\Action::make('packing')
                    ->label('Packing')
                    ->icon('heroicon-o-cube')
                    ->button()
                    ->color('success')
                    ->action(function (array $data, Order $record) {
                        $record->update(['status' => 'packing']);
                    }),

                Actions\EditAction::make()->button(),

                Actions\Action::make('Clone')
                    ->button()
                    ->action(function (Order $order) {

                        // Eager load necessary relationships
                        $orderToClone = Order::with('customer', 'address', 'items')->find($order->id);

                        if ($orderToClone) {
                            $cloneOrder = $orderToClone->replicate();

                            $cloneOrder->created_at = now();
                            $cloneOrder->save();

                            // Check if customer relationship exists
                            if ($orderToClone->customer) {
                                $cloneCustomer = $orderToClone->customer->replicate();
                                $cloneCustomer->save();
                                $cloneOrder->customer()->associate($cloneCustomer);
                            }

                            // Check if address relationship exists
                            if ($orderToClone->address) {
                                $cloneAddress = $orderToClone->address->replicate();
                                $cloneAddress->save();
                                $cloneOrder->address()->associate($cloneAddress);
                            }

                            $cloneItems = collect();

                            // Check if items relationship exists
                            if ($orderToClone->items) {
                                foreach ($orderToClone->items as $itemToClone) {
                                    $clonedItem = $itemToClone->replicate();
                                    $clonedItem->save();
                                    $cloneItems->push($clonedItem);
                                }
                            }

                            $cloneOrder->save();

                            // Associate cloned items to cloned order
                            $cloneOrder->items()->saveMany($cloneItems);

                            return $cloneOrder->id;
                        }
                        return null;
                    })
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([

                    Actions\BulkAction::make('Download pdf')
                        ->action(function (Collection $records) {
                            $recordIds = $records->pluck('id')->toArray();

                            if (count($recordIds) === 0) {
                                return response()->json(['message' => 'No records selected.']);
                            }

                            $apiUrl = route('orders.bulk-download', ['order_ids' => $recordIds]);

                            return redirect($apiUrl);
                        })
                        ->icon('heroicon-o-document-arrow-down'),

                Actions\DeleteBulkAction::make(),

                ExportBulkAction::make('Inventory')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        if (count($recordIds) === 0) {
                            return response()->json(['message' => 'No records selected.']);
                        }

                        $apiUrl = route('orders.SelectedOrders', ['order_ids' => $recordIds]);

                        return redirect($apiUrl);
                    })
                    ->icon('heroicon-o-document-arrow-down')
                    ->label('Inventory'),

                SendWhatsAppBulkAction::make(),

                Actions\BulkAction::make('Bulk Packing')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        Order::whereIn('id', $recordIds)->update(['status' => 'packing']);
                    })
                    ->icon('heroicon-o-gift'),

                Actions\BulkAction::make('Bulk Refund')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        Order::whereIn('id', $recordIds)->update(['status' => 'refund']);
                    })
                    ->icon('heroicon-o-arrow-uturn-right'),
                ]),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        return static::applyYearFilter(
            parent::getEloquentQuery()
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ])
        );
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfirmOrders::route('/'),
            'create' => Pages\CreateConfirmOrder::route('/create'),
            'edit' => Pages\EditConfirmOrder::route('/{record}/edit'),
        ];
    }
}
