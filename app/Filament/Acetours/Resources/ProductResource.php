<?php

namespace App\Filament\Acetours\Resources;

use App\Filament\Acetours\Resources\ProductResource\Pages;
use App\Filament\Acetours\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Product Details')
                    ->tabs([
                        Tab::make('General Info')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')->required(),
                                        TextInput::make('duration')->required(),
                                    ]),
                                TextInput::make('capacity')->numeric()->required(),
                                TextInput::make('trip_length')->required(),
                                DatePicker::make('date_from')->required(),
                                DatePicker::make('date_until')->required(),
                                Select::make('city_id')
                                    ->label('City')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('purchase_currency_id')
                                    ->label('Purchase Currency')
                                    ->relationship('purchase_currency', 'code')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('sales_currency_id')
                                    ->label('sales Currency')
                                    ->relationship('sales_currency', 'code')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Textarea::make('description'),
                            ]),

                        Tab::make('Product Images')
                            ->schema([
                                TextInput::make('thumbnail_image')->required(),
                            ]),

                        Tab::make('Pricing')
                            ->schema([
                                Repeater::make('product_prices')
                                    ->label('Pricing')
                                    ->schema([
                                        TextInput::make('level')
                                            ->numeric()
                                            ->minValue(1) // Minimal level 1
                                            ->required()
                                            ->label('Level'),
                                        TextInput::make('purchase_adult')
                                            ->numeric()
                                            ->required()
                                            ->label('Purchase Adult'),
                                        TextInput::make('sales_adult')
                                            ->numeric()
                                            ->required()
                                            ->label('Sales Adult'),
                                        TextInput::make('purchase_child')
                                            ->numeric()
                                            ->required()
                                            ->label('Purchase Child'),
                                        TextInput::make('sales_child')
                                            ->numeric()
                                            ->required()
                                            ->label('Sales Child'),
                                    ])
                                    ->minItems(1) // Minimal satu harga
                                    ->collapsible(), // Bikin bisa collapse tiap item
                            ]),

                        Tab::make('Location')
                            ->schema([
                                TextInput::make('latitude')->numeric(),
                                TextInput::make('longitude')->numeric(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->label('Name'),
                Tables\Columns\TextColumn::make('duration')->searchable()->label('Duration'),
                Tables\Columns\TextColumn::make('capacity')->searchable()->label('Capacity'),
                Tables\Columns\TextColumn::make('trip_length')->searchable()->label('Trip Length'),
                Tables\Columns\TextColumn::make('date_from')->searchable()->label('Date From'),
                Tables\Columns\TextColumn::make('date_until')->searchable()->label('Date Until'),
                Tables\Columns\TextColumn::make('city.name')->searchable()->label('City'),
                Tables\Columns\TextColumn::make('purchase_currency.name')->searchable()->label('Purchase Currency'),
                Tables\Columns\TextColumn::make('sales_currency.name')->searchable()->label('Sales Currency'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
