<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\AllOrdersResource\Pages;
use App\Filament\Resources\AllOrdersResource\RelationManagers;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AllOrdersResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Order::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Orders';
    protected static ?string $navigationLabel = 'All Orders';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 8;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereYear('created_at', static::getSelectedYear())->count();
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ->headerActions([
                Actions\Action::make('statusSummary')
                    ->label('Status Summary')
                    ->view('filament.resources.order-status-summary'),
            ])
            ->filters([
                Filter::make('Updated_at')
                    ->form([
                        DatePicker::make('From_date'),
                        DatePicker::make('To_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['From_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['To_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Actions\Action::make('Download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->button()
                    ->color('info')
                    ->url(
                        fn (Order $record): string => route('admin.orders.download', ['id' => $record->id]),
                        shouldOpenInNewTab: true
                    ),
                Actions\RestoreAction::make()->button(),
                Actions\ReplicateAction::make()->button()
                    ->action(function (Model $order) {

                        $orderToClone = Order::with('customer', 'address', 'items')->find($order->id);

                        if ($orderToClone) {
                            $cloneOrder = $orderToClone->replicate();

                            $cloneOrder->created_at = now();
                            $cloneOrder->save();

                            $cloneCustomer = $orderToClone->customer->replicate();
                            $cloneCustomer->save();

                            $cloneAddress = $orderToClone->address->replicate();
                            $cloneAddress->save();

                            $cloneItems = collect();

                            foreach ($orderToClone->items as $itemToClone) {
                                $clonedItem = $itemToClone->replicate();
                                $clonedItem->save();
                                $cloneItems->push($clonedItem);
                            }

                            $cloneOrder->customer()->associate($cloneCustomer);
                            $cloneOrder->address()->associate($cloneAddress);
                            $cloneOrder->save();

                            $cloneOrder->items()->saveMany($cloneItems);

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
            ])->paginated([10, 30, 40, 50, 75, 100 => 'all']);
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
            'index' => Pages\ListAllOrders::route('/'),
            'create' => Pages\CreateAllOrders::route('/create'),
            'edit' => Pages\EditAllOrders::route('/{record}/edit'),
        ];
    }
}
