<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\Filter;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Tables;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Support\Collection;


class OrderResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Order';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'placed')->whereYear('created_at', static::getSelectedYear())->count();
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
                                'cancelled' => 'cancelled',
                            ])->required(),


                ])->columns()


                ]);
    }

    public static function table(Table $table): Table
    {
        return $table

        ->query(function () {
            return Order::query()
                ->with(['customer', 'address'])
                ->where('status', 'placed')
                ->whereYear('created_at', session('admin_selected_year', now()->year))
                ->latest('created_at');
        })
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order ID')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('customer.mobile_number')->label('Mobile number')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('customer.whatsapp_number')->label('Whatsapp number')->searchable()->toggleable(isToggledHiddenByDefault: true)->sortable(),
                // Tables\Columns\TextColumn::make('address.address')->toggleable(),
                Tables\Columns\TextColumn::make('address.city_town')->label('City/Town')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('net_total')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->toggleable(isToggledHiddenByDefault: true)->sortable(),
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

                Actions\Action::make('payment')
                    ->label('Payment')
                    ->icon('heroicon-o-banknotes')
                    ->button()
                    ->color('success')
                    ->form([
                        Select::make('bank_account_id')
                            ->label('Payment Received')
                            ->options(BankAccount::all()->pluck('bank_name', 'id'))
                            ->searchable(),

                        DateTimePicker::make('payment_received_date')
                            ->default(now())
                            ->label('Payment Received Date'),
                    ])

                    ->action(function (array $data, Payment $record): void {
                        $record->bankAccount()->associate($data['bank_account_id']);
                        $record->save();
                    })

                    ->action(function (array $data, Order $record) {

                        Payment::create([
                            'order_id' => $record->id,
                            'bank_account_id' => $data['bank_account_id'],
                            'payment_received_date' => $data['payment_received_date'],
                        ]);

                        $record->update(['status' => 'payment_received']);
                    }),

                Actions\EditAction::make()->button(),

                Actions\Action::make('dispatched')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->button()
                    ->color('danger')
                    ->action(function (array $data, Order $record) {
                        $record->update(['status' => 'cancelled']);
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make('delete')
                        ->action(fn (Collection $records) => $records->each->delete())
                        ->deselectRecordsAfterCompletion(),

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


                        Actions\BulkAction::make('Bulk Cancel')
                        ->action(function (Collection $records) {
                            $recordIds = $records->pluck('id')->toArray();
                            Order::whereIn('id', $recordIds)->update(['status' => 'cancelled']);
                        })->icon('heroicon-o-x-circle'),

                        Actions\BulkAction::make('Bulk Refund')
                        ->action(function (Collection $records) {
                            $recordIds = $records->pluck('id')->toArray();
                            Order::whereIn('id', $recordIds)->update(['status' => 'refund']);
                        })->icon('heroicon-o-arrow-uturn-right'),

                        SendWhatsAppBulkAction::make(),

                        Actions\BulkAction::make('Bulk Payment')
                        ->form([
                            Select::make('bank_account_id')
                                ->label('Payment Received')
                                ->options(BankAccount::all()->pluck('bank_name', 'id'))
                                ->searchable(),

                            DateTimePicker::make('payment_received_date')
                                ->default(now())
                                ->label('Payment Received Date'),
                        ])->action(function (Collection $records, array $data) {
                            $recordIds = $records->pluck('id')->toArray();
                            if (count($recordIds) === 0) {
                                return response()->json(['message' => 'No records selected.']);
                            }
                            foreach ($records as $record) {
                                Payment::create([
                                    'order_id' => $record->id,
                                    'bank_account_id' => $data['bank_account_id'],
                                    'payment_received_date' => $data['payment_received_date'],
                                ]);
                            }
                            $records->each(function ($record) {
                                $record->update(['status' => 'payment_received']);
                            });
                        })->icon('heroicon-o-x-circle'),
                    ]),

                    ])->paginated([10, 30, 40, 50, 75, 100 => 'all'])
                    ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyYearFilter(parent::getEloquentQuery());
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
