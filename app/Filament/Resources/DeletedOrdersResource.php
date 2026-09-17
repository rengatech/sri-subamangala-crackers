<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeletedOrdersResource\Pages;
use App\Filament\Resources\DeletedOrdersResource\RelationManagers;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Tables;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeletedOrdersResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Order::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Deleted Orders';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::onlyTrashed()->whereYear('created_at', static::getSelectedYear())->count();
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

            $query = Order::query();

            return $query->onlyTrashed()->whereYear('created_at', session('admin_selected_year', now()->year));
        })

            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order ID')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer name')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('customer.mobile_number')->label('Mobile number')->toggleable()->searchable(),
                // Tables\Columns\TextColumn::make('address.address')->toggleable(),
                Tables\Columns\TextColumn::make('address.city_town')->label('City/Town')->toggleable(),
                Tables\Columns\TextColumn::make('net_total')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                ->dateTime('d-m-y H:i:s')
                ->sortable()
                ->toggleable(),
            ])
            ->filters([

            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make()
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListDeletedOrders::route('/'),
            'create' => Pages\CreateDeletedOrders::route('/create'),
            'edit' => Pages\EditDeletedOrders::route('/{record}/edit'),
        ];
    }
}
