<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendWhatsAppBulkAction;
use App\Filament\Resources\DispatchResource\Pages;
use App\Filament\Resources\DispatchResource\RelationManagers;
use App\Models\Dispatch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use App\Filament\Traits\HasYearFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Services\WhatsAppApiService;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Actions\BulkAction;

class DispatchResource extends Resource
{
    use HasYearFilter;

    protected static ?string $model = Dispatch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'dispatched')->whereYear('created_at', static::getSelectedYear())->count();
    }
    public static $related = ItemsRelationManager::class;


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Customer Details')
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(Customer::all()?->pluck('name', 'id')?->filter()?->toArray() + [null => 'No customer found'])
                        ->searchable(),

                    Select::make('address_id')
                        ->label('Address')
                        ->options(Address::all()?->pluck('address', 'id')?->filter()?->toArray() + [null => 'No address found'])
                        ->searchable(),

                    Select::make('city_town')
                        ->label('City/Town')
                        ->options(Address::all()?->pluck('city_town', 'city_town')?->filter()?->toArray() + [null => 'No city found'])
                        ->searchable(),
                ])->columns(2),
            Section::make('Dispatch Details')
                ->schema([
                    Select::make('order_id')
                        ->label('Order_Id')
                        ->options(Order::all()->pluck('id', 'id'))
                        ->searchable(),

                    DateTimePicker::make('book_date'),

                    DateTimePicker::make('delivery_date'),

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
                        ->nullable(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')->label('Order_Id')->searchable()->toggleable()->sortable(),
                TextColumn::make('order.customer.name')
                    ->label('Customer Name')
                    ->searchable(),
                TextColumn::make('order.customer.mobile_number')
                    ->label('Mobile Number')
                    ->searchable(),
                TextColumn::make('order.address.city_town')
                    ->label('City')
                    ->searchable(),
                ImageColumn::make('lr_screenshot_path')
                    ->label('LR Screenshot')
                    ->disk('public')
                    ->height(60)
                    ->openUrlInNewTab()
                    ->url(fn (Dispatch $record): ?string => $record->lr_screenshot_path ? asset('storage/' . $record->lr_screenshot_path) : null),
                Tables\Columns\TextColumn::make('book_date')->searchable()
                    ->dateTime('d-m-y H:i:s')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('delivery_date')->label('Delivery_Date')->searchable()
                    ->dateTime('d-m-y H:i:s')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated date')->searchable()->toggleable(isToggledHiddenByDefault: true),

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
                })
            ])


            ->actions([
                Actions\Action::make('download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->button()
                    ->color('info')
                    ->url(
                        fn (Dispatch $record): string => route('admin.orders.download', ['id' => $record->order_id]),
                        shouldOpenInNewTab: true
                    ),
                Actions\Action::make('resend_lr')
                    ->label('Resend LR')
                    ->icon('heroicon-o-paper-airplane')
                    ->button()
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Resend LR Screenshot')
                    ->modalDescription('This will resend the LR screenshot via WhatsApp to the customer.')
                    ->visible(fn (Dispatch $record): bool => (bool) $record->lr_screenshot_path)
                    ->action(function (Dispatch $record) {
                        $record->loadMissing('order.customer');
                        $customer = $record->order?->customer;

                        if (!$customer || !$record->lr_screenshot_path) {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed')
                                ->body('Customer or LR screenshot not found.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $whatsappService = app(WhatsAppApiService::class);
                        $result = $whatsappService->sendOrderDispatchMessage(
                            $customer->whatsapp_number,
                            $customer->name,
                            Storage::disk('public')->url($record->lr_screenshot_path),
                            Storage::disk('public')->path($record->lr_screenshot_path),
                        );

                        if ($result) {
                            \Filament\Notifications\Notification::make()
                                ->title('Sent')
                                ->body('LR screenshot resent via WhatsApp.')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed')
                                ->body('Failed to send WhatsApp message. Check logs.')
                                ->danger()
                                ->send();
                        }
                    }),
                Actions\ViewAction::make()->button(),
                Actions\EditAction::make()->button(),
                Actions\DeleteAction::make()->button(),
            ])

            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                ExportBulkAction::make('inventory')
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

                Actions\BulkAction::make('Bulk Refund')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        Order::whereIn('id', $recordIds)->update(['status' => 'refund']);
                    })->icon('heroicon-o-arrow-uturn-right'),

                Actions\BulkAction::make('Bulk Cancel')
                    ->action(function (Collection $records) {
                        $recordIds = $records->pluck('id')->toArray();
                        Order::whereIn('id', $recordIds)->update(['status' => 'cancel']);
                    })->icon('heroicon-o-x-circle'),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make(),
            ])->paginated([10, 30, 40, 50, 75, 100 => 'all'])->defaultSort('created_at', 'desc');

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
            'index' => Pages\ListDispatches::route('/'),
            'create' => Pages\CreateDispatch::route('/create'),
            'edit' => Pages\EditDispatch::route('/{record}/edit'),
        ];
    }
}
