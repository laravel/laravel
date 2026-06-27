<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $modelLabel = 'Produk';
    protected static ?string $pluralModelLabel = 'Daftar Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Game'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Produk')
                    ->placeholder('86 Diamonds'),
                Forms\Components\TextInput::make('supplier_code')
                    ->required()
                    ->label('Kode Supplier')
                    ->placeholder('ML86'),
                Forms\Components\TextInput::make('base_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga Dasar (Beli)')
                    ->placeholder('10000'),
                Forms\Components\TextInput::make('sell_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga Jual')
                    ->placeholder('12000'),
                Forms\Components\Toggle::make('status')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Game')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier_code')
                    ->label('Kode Supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->money('IDR')
                    ->label('Harga Dasar'),
                Tables\Columns\TextColumn::make('sell_price')
                    ->money('IDR')
                    ->label('Harga Jual'),
                Tables\Columns\TextColumn::make('profit')
                    ->state(fn ($record) => $record->sell_price - $record->base_price)
                    ->money('IDR')
                    ->label('Keuntungan'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Game'),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
