<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions;
use Filament\Tables;
use Closure;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;

class ProductRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                  
                Forms\Components\TextInput::make('tamil_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->reactive()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        $set('url_slug', str::slug($state));
                    }),

                Forms\Components\TextInput::make('url_slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('seo_title')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('description')
                    ->required(),


                Forms\Components\TextInput::make('price')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('image')->image()
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('tamil_name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('url_slug'),
                Tables\Columns\TextColumn::make('seo_title'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('price'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }
}
