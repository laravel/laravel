<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon  = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('target_id')
                ->label('User ID')
                ->required(),

            Forms\Components\TextInput::make('target_zone')
                ->label('Zone / Server ID')
                ->nullable(),

            Forms\Components\Select::make('status')
                ->options([
                    'pending'    => 'Pending',
                    'paid'       => 'Paid',
                    'processing' => 'Processing',
                    'success'    => 'Success',
                    'failed'     => 'Failed',
                    'expired'    => 'Expired',
                ])
                ->required(),

            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->prefix('Rp')
                ->required(),

            Forms\Components\TextInput::make('payment_ref')
                ->label('Payment Ref (Tripay)')
                ->readOnly(),

            Forms\Components\TextInput::make('supplier_ref')
                ->label('Supplier Ref')
                ->readOnly(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->formatStateUsing(fn ($state) => "ARC-{$state}")
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.category.name')
                    ->label('Game'),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk'),

                Tables\Columns\TextColumn::make('target_id')
                    ->label('Target'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                // FIX: BadgeColumn deprecated → use TextColumn->badge()
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'paid'       => 'info',
                        'processing' => 'info',
                        'success'    => 'success',
                        'failed'     => 'danger',
                        'expired'    => 'gray',
                        default      => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'paid'       => 'Paid',
                        'processing' => 'Processing',
                        'success'    => 'Success',
                        'failed'     => 'Failed',
                        'expired'    => 'Expired',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
