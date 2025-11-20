<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationLabel = 'My Services';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Service Name')
                    ->disabled(),
                TextInput::make('type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->disabled(),
                TextInput::make('status')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->disabled(),
                TextInput::make('price')
                    ->prefix('$')
                    ->disabled(),
                DatePicker::make('renewal_date')
                    ->disabled(),
                KeyValue::make('details')
                    ->label('Service Details')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'success',
                        'suspended' => 'warning',
                        'cancelled' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('renewal_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->toolbarActions([
                //
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
            'index' => Pages\ListServices::route('/'),
            'view' => Pages\ViewService::route('/{record}'),
        ];
    }
}
