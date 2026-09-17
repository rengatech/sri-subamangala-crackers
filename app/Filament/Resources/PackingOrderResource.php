<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\PackingOrderResource\Pages;
use App\Filament\Resources\PackingOrderResource\RelationManagers;
use App\Models\Order;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\Orderstatus;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Dispatch;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Support\Collection;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackingOrderResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Packing Orders';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'packing')->whereYear('created_at', static::getSelectedYear())->count();
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
                                'dispatched' => 'dispatched',
                                'packing' => 'packing',
                                'cancel' => 'cancel',

                            ])
                            ->required(),

                        FileUpload::make('lr_screenshot')
                            ->label('Upload LR Screenshot')
                            ->image()
                            ->imagePreviewHeight('150')
                            ->directory('lr-screenshots')
                            ->disk('public')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes(['image/*'])
                            ->maxSize(10240)
                            ->extraInputAttributes(['capture' => 'environment'])
                            ->nullable(),

                        Hidden::make('photo_data'),

                        Placeholder::make('camera_input')
                            ->label('Laptop Camera')
                            ->content(new HtmlString(view('filament.pages.webcam-upload')->render())),

                    ])->columns()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->query(function () {

                $query = Order::query();

                return $query->with(['customer', 'address'])->where('status', 'packing')->whereYear('created_at', session('admin_selected_year', now()->year))->latest('created_at');
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['To_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Actions\Action::make('download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->button()
                    ->color('info')
                    ->url(
                        fn(Order $record): string => route('admin.orders.download', ['id' => $record->id]),
                        shouldOpenInNewTab: true
                    ),

                Actions\Action::make('dispatched')
                    ->label('Dispatch')
                    ->icon('heroicon-o-truck')
                    ->button()
                    ->color('success')
                    ->form([
                        FileUpload::make('lr_screenshot_path')
                            ->label('Upload LR Screenshot')
                            ->image()
                            ->imagePreviewHeight('150')
                            ->directory('lr-screenshots')
                            ->disk('public')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes(['image/*'])
                            ->maxSize(10240)
                            ->extraInputAttributes(['capture' => 'environment'])
                            ->nullable(),
                        DatePicker::make('book_date')->label('Book Date')->default(now()),
                        DatePicker::make('delivery_date')->label('Delivery Date')->default(now()),
                    ])

                    ->action(function (array $data, Order $record) {
                        $lrPath = $data['lr_screenshot_path'] ?? null;

                        Dispatch::create([
                            'order_id' => $record->id,
                            'lr_screenshot_path' => $lrPath,
                            'book_date' => $data['book_date'],
                            'delivery_date' => $data['delivery_date'],
                        ]);

                        $record->update(['status' => 'dispatched']);

                        if ($lrPath) {
                            $record->loadMissing('customer');
                            $url = Storage::disk('public')->url($lrPath);
                            $text = "{$record->customer->name}, Order ID: {$record->id} your order is dispatched. LR image: {$url}";
                            $whatsappLink = 'https://api.whatsapp.com/send?phone=91' . $record->customer->whatsapp_number . '&text=' . urlencode($text);

                            \Filament\Notifications\Notification::make()
                                ->title('Order dispatched')
                                ->success()
                                ->actions([
                                    Actions\Action::make('whatsapp')
                                        ->label('Via WhatsApp')
                                        ->url($whatsappLink)
                                        ->openUrlInNewTab()
                                        ->button(),
                                ])
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Order dispatched')
                                ->success()
                                ->send();
                        }
                    }),

                Actions\EditAction::make()->button(),

                Actions\ReplicateAction::make()->button()
                    ->action(function (Order $order) {
                        $orderToClone = Order::with('customer', 'address', 'items')->find($order->id);

                        if ($orderToClone) {
                            $cloneOrder = $orderToClone->replicate();
                            $cloneOrder->created_at = now();
                            $cloneOrder->save();

                            if ($orderToClone->customer) {
                                $cloneCustomer = $orderToClone->customer->replicate();
                                $cloneCustomer->save();
                                $cloneOrder->customer()->associate($cloneCustomer);
                            }

                            if ($orderToClone->address) {
                                $cloneAddress = $orderToClone->address->replicate();
                                $cloneAddress->save();
                                $cloneOrder->address()->associate($cloneAddress);
                            }

                            $cloneItems = collect();

                            foreach ($orderToClone->items as $itemToClone) {
                                if ($itemToClone) {
                                    $clonedItem = $itemToClone->replicate();
                                    $clonedItem->save();
                                    $cloneItems->push($clonedItem);
                                }
                            }

                            $cloneOrder->save();

                            if ($cloneItems->isNotEmpty()) {
                                $cloneOrder->items()->saveMany($cloneItems);
                            }

                            return $cloneOrder->id;
                        }

                        return null;
                    })

            ])
            ->bulkActions([

                Actions\DeleteBulkAction::make(),
                Actions\BulkAction::make('Download Pdf')
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

                SendWhatsAppBulkAction::make(),

                BulkAction::make('Bulk Dispatch')
                    ->form([
                        FileUpload::make('lr_screenshot_path')
                            ->label('Upload LR Screenshot')
                            ->image()
                            ->imagePreviewHeight('150')
                            ->directory('lr-screenshots')
                            ->disk('public')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes(['image/*'])
                            ->maxSize(10240)
                            ->extraInputAttributes(['capture' => 'environment'])
                            ->nullable(),
                        DatePicker::make('book_date')->label('Book Date')->default(now()),
                        DatePicker::make('delivery_date')->label('Delivery Date')->default(now()),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $recordIds = $records->pluck('id')->toArray();
                        if (count($recordIds) === 0) {
                            return response()->json(['message' => 'No records selected.']);
                        }
                        $lrPath = $data['lr_screenshot_path'] ?? null;
                        $whatsappService = app(\App\Services\WhatsAppApiService::class);

                        foreach ($records as $record) {
                            Dispatch::create([
                                'order_id' => $record->id,
                                'lr_screenshot_path' => $lrPath,
                                'book_date' => $data['book_date'],
                                'delivery_date' => $data['delivery_date'],
                            ]);

                            $record->update(['status' => 'dispatched']);

                            if ($lrPath) {
                                $record->loadMissing('customer');
                                $whatsappService->sendOrderDispatchMessage(
                                    $record->customer->whatsapp_number,
                                    $record->customer->name,
                                    Storage::disk('public')->url($lrPath),
                                    Storage::disk('public')->path($lrPath),
                                );
                            }
                        }
                    }),

                Actions\BulkAction::make('Bulk cancel')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        Order::whereIn('id', $recordIds)->update(['status' => 'cancel']);
                    })->icon('heroicon-o-x-circle'),

                Actions\BulkAction::make('Bulk Refund')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        Order::whereIn('id', $recordIds)->update(['status' => 'refund']);
                    })->icon('heroicon-o-arrow-uturn-right'),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyYearFilter(parent::getEloquentQuery());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackingOrders::route('/'),
            'create' => Pages\CreatePackingOrder::route('/create'),
            'edit' => Pages\EditPackingOrder::route('/{record}/edit'),
        ];
    }
}