<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

            TextInput::make('mobile_number_1')
                ->label('Mobile Number 1')
                ->required(),
            TextInput::make('mobile_number_2')
                ->label('Mobile Number 2')
                ->required(),
            TextInput::make('mobile_number_3')
                ->label('Mobile Number 3')
                ->required(),
            TextInput::make('mobile_number_4')
                ->label('Mobile Number 4')
                ->required(),

            TextInput::make('mobile_number_5')
                ->label('Mobile Number 5')
                ->required(),

            Textarea::make('marquee_content')
                ->label('Marquee Content')
                ->required()
                ->maxLength(455),

        ]);
    }
}
