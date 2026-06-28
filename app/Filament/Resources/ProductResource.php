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

    protected static ?string $navigationIcon  = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Produk')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Game')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama Produk')
                        ->placeholder('86 Diamonds')
                        ->required(),

                    Forms\Components\TextInput::make('base_price')
                        ->label('Harga Modal (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Forms\Components\TextInput::make('sell_price')
                        ->label('Harga Jual (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Forms\Components\Toggle::make('status')
                        ->label('Aktif')
                        ->default(true)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Konfigurasi Supplier')
                ->description('Isi kode produk dari masing-masing supplier.')
                ->schema([
                    Forms\Components\TextInput::make('supplier_code')
                        ->label('Kode Digiflazz (Primary)')
                        ->placeholder('mlbb-86-diamonds')
                        ->helperText('Cek di Digiflazz → Produk → Daftar Harga. Kolom "SKU".')
                        ->required(),

                    Forms\Components\KeyValue::make('supplier_codes')
                        ->label('Kode Supplier Lain (Optional)')
                        ->keyLabel('Nama Supplier (key)')
                        ->valueLabel('Kode Produk')
                        ->addButtonLabel('+ Tambah Supplier')
                        ->helperText('Key harus sesuai: digiflazz, vipreseller. Kosongkan jika tidak ada backup supplier.')
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Game')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier_code')
                    ->label('SKU Digiflazz')
                    ->searchable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Modal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sell_price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('margin')
                    ->label('Margin')
                    ->getStateUsing(fn ($record) => $record->sell_price - $record->base_price)
                    ->money('IDR'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('category_id')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Game')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
