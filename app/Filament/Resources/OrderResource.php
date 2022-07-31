<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-list';
    protected static ?string $navigationGroup = 'Artworks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('orderno')->required(),

                Select::make('orders_customer_id')
                    ->label('Customer')
                    ->options(Customer::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('status')
                    ->options([
                        'neworder' => 'New Order',
                        'inprocess' => 'In Process',
                        'noartwork' => 'No Artworks',
                        'approved' => 'Approved',
                        'cancelled' => 'Cancelled',
                        'printed' => 'Printed',
                        'delivered' => 'Delivered',
                    ])
                    ->required(),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('orderno')
                    ->searchable()
                    ->sortable()
                    ->label('Order No'),
                TextColumn::make('customer.name'),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'neworder',
                        'success' => 'inprocess',
                        'warning' => 'noartwork',
                        'success' => 'approved',
                        'warning' => 'cancelled',
                        'success' => 'printed',
                        'warning' => 'delivered',
                    ])->searchable(),
                TextColumn::make('updated_at')
                    ->sortable(),


                // 'neworder','inprocess','noartwork','approved','cancelled','printed','delivered'



            ])->defaultSort('orderno', 'desc')

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
            RelationManagers\ArtworksRelationManager::class,

        ];
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}