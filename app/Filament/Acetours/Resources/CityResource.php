<?php

namespace App\Filament\Acetours\Resources;

use App\Filament\Acetours\Resources\CityResource\Pages;
use App\Filament\Acetours\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name') // Relasi dengan tabel country
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('postal_code')->required(),
                TextInput::make('latitude')->required(),
                TextInput::make('longitude')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('Name'),
                TextColumn::make('country.name')->searchable()->label('Country'),
                TextColumn::make('postal_code')->searchable()->label('Postal Code'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CityResource\RelationManagers\CountryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
