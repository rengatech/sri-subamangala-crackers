<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;


class Settings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('global_discount')
                ->label('Global discount ')
                ->required(),

            TextInput::make('min_order_value')
                ->label('Min order value')
                ->required(),

            TextInput::make('starting_year')
                ->label('diwali sales starting year')
                ->required(),

            TextInput::make('company_name')
                ->label('Company Name')
                ->required(),

            TextInput::make('company_address')
                ->label('Company Address')
                ->required(),

            TextInput::make('email_id')
                ->label('Email ID')
                ->email()
                ->required(),

            TextInput::make('website')
                ->label('Website')
                ->url()
                ->required(),

            \Filament\Forms\Components\FileUpload::make('logo')
                ->label('Logo')
                ->image()
                ->directory('settings'),

            Repeater::make('mobile_numbers')
                ->label('Mobile Numbers')
                ->simple(
                    TextInput::make('number')
                        ->label('Mobile Number')
                        ->tel()
                        ->required()
                )
                ->addActionLabel('+ Add Mobile Number')
                ->reorderable(false)
                ->minItems(1)
                ->required()
                ->columnSpanFull(),

            TextInput::make('whatsapp_number')
                ->label('WhatsApp Number')
                ->tel()
                ->required()
                ->helperText('Include country code, e.g. 919876543210 (used for click-to-chat links)'),

            Textarea::make('marquee_content')
                ->label('Marquee Content')
                ->required()
                ->maxLength(455),

        ]);
    }
}
