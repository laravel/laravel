<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon  = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Game';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Game')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Game')
                        ->required(),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required(),

                    Forms\Components\TextInput::make('icon')
                        ->label('Icon (Emoji)')
                        ->placeholder('🎮'),

                    Forms\Components\Toggle::make('status')
                        ->label('Aktif')
                        ->default(true),
                ]),

            Forms\Components\Section::make('Pengaturan Input Akun')
                ->description('Konfigurasi input yang dibutuhkan saat order.')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('need_zone')
                        ->label('Butuh Zone / Server ID?')
                        ->helperText('Aktifkan untuk game seperti ML, Genshin, HSR')
                        ->reactive()
                        ->default(false),

                    Forms\Components\TextInput::make('target_label')
                        ->label('Label User ID')
                        ->placeholder('User ID')
                        ->default('User ID')
                        ->helperText('Contoh: User ID, UID, Player ID'),

                    Forms\Components\TextInput::make('zone_label')
                        ->label('Label Zone / Server')
                        ->placeholder('Server ID')
                        ->default('Server ID')
                        ->visible(fn (Forms\Get $get) => $get('need_zone'))
                        ->helperText('Contoh: Server ID, Server, Zone'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->width(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Game')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('need_zone')
                    ->label('Zone?')
                    ->boolean(),

                Tables\Columns\TextColumn::make('target_label')
                    ->label('Input Utama'),

                Tables\Columns\TextColumn::make('zone_label')
                    ->label('Input Zone'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Produk')
                    ->counts('products'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('id')
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
