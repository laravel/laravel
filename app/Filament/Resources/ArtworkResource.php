<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtworkResource\Pages;
use App\Filament\Resources\ArtworkResource\RelationManagers;
use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;


class ArtworkResource extends Resource
{
    protected static ?string $model = Artwork::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Artworks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')->required(),

                Select::make('artworks_order_id')
                    ->label('Order')
                    ->options(Order::all()->pluck('orderno', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('requiredqty')->required(),
                TextInput::make('jobrun'),
                TextInput::make('labelrepeat'),
                TextInput::make('printedqty'),
                TextInput::make('artworks_media_id'),
                // $table->id();
                // $table->string('description');
                // $table->bigInteger('artworks_order_id');
                // $table->integer('requiredqty');
                // $table->integer('jobrun');
                // $table->integer('labelrepeat');
                // $table->integer('printedqty');
                // $table->bigInteger('artworks_media_id');
                // $table->timestamps();
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->label('Description'),
                TextColumn::make('requiredqty'),
                TextColumn::make('jobrun'),
                TextColumn::make('labelrepeat'),
                TextColumn::make('printedqty'),
                TextColumn::make('order.orderno'),

                TextColumn::make('created_at'),
                TextColumn::make('updated_at'),






            ])->defaultSort('id', 'desc')

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListArtworks::route('/'),
            'create' => Pages\CreateArtwork::route('/create'),
            'edit' => Pages\EditArtwork::route('/{record}/edit'),
        ];
    }
}