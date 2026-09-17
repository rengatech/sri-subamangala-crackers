<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\RefundOrdersResource\Pages;
use App\Filament\Resources\RefundOrdersResource\RelationManagers\ItemsReletionManager;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RefundOrdersResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';
    protected static ?string $navigationLabel = 'Refund Orders';
    protected static ?int $navigationSort = 7;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'refund')->whereYear('created_at', static::getSelectedYear())->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Customer Details')
                    ->schema([

                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(Customer::all()?->pluck('name', 'id')?->filter()?->toArray() + [null => 'No customer found'])
                            ->required()
                            ->searchable(),

                        Select::make('address_id')
                            ->label('Address')
                            ->options(Address::all()?->pluck('address', 'id')?->filter()?->toArray() + [null => 'No address found'])
                            ->required()
                            ->searchable(),

                        Select::make('city_town')
                            ->label('City/Town')
                            ->options(Address::all()?->pluck('city_town', 'city_town')?->filter()?->toArray() + [null => 'No city found'])
                            ->searchable(),

                    ])->columns(2),

                Section::make('Order Summary')

                    ->schema([

                        Forms\Components\TextInput::make('net_total')
                            ->reactive()
                            ->required(),

                        Forms\Components\TextInput::make('notes')
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->options([
                                'placed' => 'placed',
                                'cancelled' => 'cancelled',
                                'refund' => 'refund',
                            ])->required(),

                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {

                $query = Order::query();

                return $query->where('status', 'refund')->whereYear('created_at', session('admin_selected_year', now()->year));
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order ID')->toggleable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer name')->toggleable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer.mobile_number')->label('Mobile number')->toggleable()->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('address.city_town')->label('City/Town')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address.pincode')->label('Pincode')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('net_total')->toggleable()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('notes')->toggleable(isToggledHiddenByDefault: true)->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->label('Ordered date')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated date')->searchable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\BulkAction::make('Bulk Place')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        Order::whereIn('id', $recordIds)->update(['status' => 'placed']);
                    })->icon('heroicon-o-x-circle'),
                ]),

                SendWhatsAppBulkAction::make(),
            ])->paginated([10, 30, 40, 50, 75, 100 => 'all']);
    }

    public static function getRelations(): array
    {
        return [
            ItemsReletionManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyYearFilter(parent::getEloquentQuery());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefundOrders::route('/'),
            'create' => Pages\CreateRefundOrders::route('/create'),
            'edit' => Pages\EditRefundOrders::route('/{record}/edit'),
        ];
    }
}
