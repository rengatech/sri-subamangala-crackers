<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\CancelOrderResource\Pages;
use App\Filament\Resources\CancelOrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\Filter;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CancelOrderResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-x-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Cancelled Orders';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected static ?string $recordTitleAttribute = 'id';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'cancelled')->whereYear('created_at', static::getSelectedYear())->count();
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

        ->query(function () {

            $query = Order::query();

            return $query->whereIn('status', ['cancelled', 'refund'])->withTrashed()->whereYear('created_at', session('admin_selected_year', now()->year));
        })

            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order ID')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer name')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('customer.mobile_number')->label('Mobile number')->toggleable()->searchable(),
                // Tables\Columns\TextColumn::make('address.address')->toggleable(),
                Tables\Columns\TextColumn::make('address.city_town')->label('City/Town')->toggleable(),
                Tables\Columns\TextColumn::make('net_total')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('status')->searchable()->toggleable(),
                SelectColumn::make('status')
                    ->options([
                        'placed' => 'placed',
                        'cancelled' => 'cancelled',
                        'refund' => 'refund',
                    ]),
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
                Actions\EditAction::make()->button(),
                Actions\RestoreAction::make()->button(),
            ])
            ->bulkActions([
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
            ])->paginated([10, 30, 40, 50, 75, 100 => 'all'])->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListCancelOrders::route('/'),
            'create' => Pages\CreateCancelOrder::route('/create'),
            'edit' => Pages\EditCancelOrder::route('/{record}/edit'),
        ];
    }
}
