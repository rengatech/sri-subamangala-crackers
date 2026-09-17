<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\Order;
use App\Models\BankAccount;
use App\Models\Customer;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Card;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereYear('created_at', static::getSelectedYear())->count();
    }


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Card::make()
                    ->schema([

                        Select::make('order_id')
                            ->label('Order id')
                            ->options(Order::all()->pluck('order_no', 'id'))
                            ->required()
                            ->searchable(),

                        Select::make('bank_account_id')
                        ->label('Payment Received')
                        ->options(BankAccount::all()->pluck('bank_name', 'id')),

                        DateTimePicker::make('payment_received_date')->label('Payment Received Date'),

                        // DateTimePicker::make('created_at')->label('created_at'),

                        // DateTimePicker::make('updated_at')->label('Updated_at'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('bankAccount.bank_name')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('payment_received_date')->searchable()
                ->date('d-m-y')
                ->sortable()
                ->toggleable()
                ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')->searchable()
                // ->dateTime('d-m-y H:i:s')
                // ->sortable()
                // ->toggleable()
                // ->searchable(),
                // Tables\Columns\TextColumn::make('updated_at')->searchable()
                // ->dateTime('d-m-y H:i:s')
                // ->sortable()
                // ->toggleable()
                // ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                // Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),

            ]);

    }


    protected function getTableHeaderActions(): array
    {
        return [
            // FilamentExportHeaderAction::make('Export'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            // FilamentExportBulkAction::make('Export'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyYearFilter(parent::getEloquentQuery());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
