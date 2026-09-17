<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsAppTemplateResource\Pages;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsAppApiService;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class WhatsAppTemplateResource extends Resource
{
    protected static ?string $model = WhatsAppTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?string $navigationLabel = 'WhatsApp Templates';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'WhatsApp Template';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', WhatsAppTemplate::STATUS_APPROVED)->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Template Details')
                    ->schema([
                        Forms\Components\TextInput::make('template_name')
                            ->label('Template Name')
                            ->required()
                            ->maxLength(512)
                            ->regex('/^[a-z0-9_]+$/')
                            ->helperText('Lowercase letters, numbers, and underscores only')
                            ->disabled(fn (?WhatsAppTemplate $record) => $record !== null),

                        Forms\Components\Select::make('category')
                            ->options([
                                'MARKETING' => 'Marketing',
                                'UTILITY' => 'Utility',
                                'AUTHENTICATION' => 'Authentication',
                            ])
                            ->required()
                            ->default('UTILITY')
                            ->disabled(fn (?WhatsAppTemplate $record) => $record !== null),

                        Forms\Components\Select::make('language_code')
                            ->label('Language')
                            ->options([
                                'ta' => 'Tamil',
                                'en' => 'English',
                                'en_US' => 'English (US)',
                                'hi' => 'Hindi',
                            ])
                            ->required()
                            ->default('ta')
                            ->disabled(fn (?WhatsAppTemplate $record) => $record !== null),
                    ])->columns(3),

                Forms\Components\Section::make('Template Content')
                    ->schema([
                        Forms\Components\TextInput::make('header_text')
                            ->label('Header (optional)')
                            ->maxLength(60)
                            ->helperText('Max 60 characters. Leave empty for no header.'),

                        Forms\Components\Textarea::make('body')
                            ->label('Body')
                            ->required()
                            ->rows(5)
                            ->maxLength(1024)
                            ->helperText('Use {{1}}, {{2}}, etc. for variables. Max 1024 characters.'),

                        Forms\Components\TextInput::make('footer_text')
                            ->label('Footer (optional)')
                            ->maxLength(60)
                            ->helperText('Max 60 characters. Leave empty for no footer.'),
                    ]),

                Forms\Components\Section::make('Status Information')
                    ->schema([
                        Forms\Components\Placeholder::make('status_display')
                            ->label('Status')
                            ->content(fn (?WhatsAppTemplate $record) => $record?->status ?? 'Will be set after Meta review'),

                        Forms\Components\Placeholder::make('quality_display')
                            ->label('Quality Score')
                            ->content(fn (?WhatsAppTemplate $record) => $record?->quality_score
                                ? (WhatsAppTemplate::QUALITY_SCORES[$record->quality_score] ?? $record->quality_score)
                                : 'N/A'),

                        Forms\Components\Placeholder::make('rejected_reason_display')
                            ->label('Rejection Reason')
                            ->content(fn (?WhatsAppTemplate $record) => $record?->rejected_reason
                                ? str_replace('_', ' ', $record->rejected_reason)
                                : 'N/A')
                            ->visible(fn (?WhatsAppTemplate $record) => $record?->rejected_reason !== null),
                    ])
                    ->columns(3)
                    ->visible(fn (?WhatsAppTemplate $record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('template_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MARKETING' => 'info',
                        'UTILITY' => 'warning',
                        'AUTHENTICATION' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('body')
                    ->limit(50)
                    ->tooltip(fn (WhatsAppTemplate $record): string => $record->body ?? ''),

                Tables\Columns\TextColumn::make('language_code')
                    ->label('Language')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'APPROVED' => 'success',
                        'PENDING' => 'warning',
                        'REJECTED' => 'danger',
                        'IN_APPEAL' => 'warning',
                        'PAUSED' => 'gray',
                        'DISABLED' => 'danger',
                        'LIMIT_EXCEEDED' => 'gray',
                        'PENDING_DELETION' => 'gray',
                        'DELETED' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match (strtoupper($state)) {
                        'APPROVED' => 'Approved',
                        'PENDING' => 'In Review',
                        'REJECTED' => 'Rejected',
                        'IN_APPEAL' => 'In Appeal',
                        'PAUSED' => 'Paused',
                        'DISABLED' => 'Disabled',
                        'LIMIT_EXCEEDED' => 'Rate Limited',
                        'PENDING_DELETION' => 'Deleting',
                        'DELETED' => 'Deleted',
                        default => $state,
                    })
                    ->description(fn (WhatsAppTemplate $record): ?string => match (true) {
                        $record->rejected_reason !== null && $record->rejected_reason !== 'NONE'
                            => str_replace('_', ' ', $record->rejected_reason),
                        $record->quality_score !== null
                            => 'Quality: ' . (WhatsAppTemplate::QUALITY_SCORES[$record->quality_score] ?? $record->quality_score),
                        default => null,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('quality_score')
                    ->label('Quality')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? (WhatsAppTemplate::QUALITY_SCORES[$state] ?? $state)
                        : 'Pending')
                    ->color(fn (?string $state): string => match ($state) {
                        'GREEN' => 'success',
                        'YELLOW' => 'warning',
                        'RED' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Synced')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(
                        collect(WhatsAppTemplate::STATUSES)
                            ->mapWithKeys(fn (string $status) => [$status => match ($status) {
                                'APPROVED' => 'Approved',
                                'PENDING' => 'In Review',
                                'REJECTED' => 'Rejected',
                                'IN_APPEAL' => 'In Appeal',
                                'PAUSED' => 'Paused',
                                'DISABLED' => 'Disabled',
                                'LIMIT_EXCEEDED' => 'Rate Limited',
                                'PENDING_DELETION' => 'Deleting',
                                'DELETED' => 'Deleted',
                            }])
                            ->toArray()
                    ),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'MARKETING' => 'Marketing',
                        'UTILITY' => 'Utility',
                        'AUTHENTICATION' => 'Authentication',
                    ]),
                Tables\Filters\SelectFilter::make('quality_score')
                    ->label('Quality')
                    ->options([
                        'GREEN' => 'High',
                        'YELLOW' => 'Medium',
                        'RED' => 'Low',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->visible(fn (WhatsAppTemplate $record): bool => $record->isEditable()),

                Actions\Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (WhatsAppTemplate $record): bool => $record->isSendable())
                    ->form(function (WhatsAppTemplate $record): array {
                        $fields = [
                            Forms\Components\TextInput::make('phone_number')
                                ->label('Phone Number')
                                ->required()
                                ->tel()
                                ->prefix('+91')
                                ->helperText('Enter 10-digit mobile number'),
                        ];

                        $variables = $record->variables ?? [];
                        foreach ($variables as $var) {
                            $fields[] = Forms\Components\TextInput::make("variable_{$var}")
                                ->label("Variable {{$var}}")
                                ->required();
                        }

                        return $fields;
                    })
                    ->action(function (WhatsAppTemplate $record, array $data): void {
                        $phone = '91' . ltrim($data['phone_number'], '0');

                        $components = [];
                        $variables = $record->variables ?? [];

                        if (!empty($variables)) {
                            $parameters = [];
                            foreach ($variables as $var) {
                                $parameters[] = [
                                    'type' => 'text',
                                    'text' => $data["variable_{$var}"],
                                ];
                            }
                            $components[] = [
                                'type' => 'body',
                                'parameters' => $parameters,
                            ];
                        }

                        $service = app(WhatsAppApiService::class);
                        $message = $service->sendTemplateMessage(
                            $phone,
                            $record->template_name,
                            $record->language_code,
                            $components
                        );

                        if ($message) {
                            Notification::make()
                                ->success()
                                ->title('Template Sent')
                                ->body("Template '{$record->template_name}' sent to {$phone}")
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('Send Failed')
                                ->body('Failed to send template message. Check logs for details.')
                                ->send();
                        }
                    }),

                Actions\DeleteAction::make()
                    ->before(function (WhatsAppTemplate $record): void {
                        $service = app(WhatsAppApiService::class);
                        $result = $service->deleteMetaTemplate($record->template_name);

                        if (!$result['success']) {
                            $error = $result['data']['error']['message'] ?? 'Unknown error';
                            Notification::make()
                                ->danger()
                                ->title('Meta API Error')
                                ->body("Failed to delete from Meta: {$error}")
                                ->send();

                            throw new \Exception('Meta deletion failed');
                        }
                    }),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->before(function (\Illuminate\Support\Collection $records): void {
                        $service = app(WhatsAppApiService::class);
                        foreach ($records as $record) {
                            $result = $service->deleteMetaTemplate($record->template_name);
                            if (!$result['success']) {
                                $error = $result['data']['error']['message'] ?? 'Unknown error';
                                Notification::make()
                                    ->danger()
                                    ->title('Meta API Error')
                                    ->body("Failed to delete '{$record->template_name}' from Meta: {$error}")
                                    ->send();

                                throw new \Exception('Meta deletion failed');
                            }
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsAppTemplates::route('/'),
            'create' => Pages\CreateWhatsAppTemplate::route('/create'),
            'edit' => Pages\EditWhatsAppTemplate::route('/{record}/edit'),
        ];
    }
}
