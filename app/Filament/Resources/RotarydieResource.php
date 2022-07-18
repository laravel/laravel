<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RotarydieResource\Pages;
use App\Filament\Resources\RotarydieResource\RelationManagers;
use App\Models\Cylinder;
use App\Models\Rotarydie;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\BooleanColumn;



class RotarydieResource extends Resource
{
    protected static ?string $model = Rotarydie::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Rotary Die';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('rotarydies_cylinder_id')
                    ->label('Teeth')
                    ->options(Cylinder::all()->pluck('teeth', 'id'))
                    ->searchable(),
                
                
                TextInput::make('customermark'),
                TextInput::make('aroundsize')->numeric(),
                TextInput::make('acrosssize')->numeric(),
                TextInput::make('aroundrepeat')->numeric(),
                TextInput::make('acrossrepeat')->numeric(),
                TextInput::make('aroundgap')->numeric(),
                TextInput::make('acrossgap')->numeric(),
                TextInput::make('cornerradius')->numeric(),
                TextInput::make('media'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('aroundsize')->limit(10)
                ->sortable()
                ->searchable()
                ->label('Around Size'),

                TextColumn::make('acrosssize')->limit(10),
                TextColumn::make('aroundrepeat')->limit(10),
                TextColumn::make('acrossrepeat')->limit(10),
                TextColumn::make('aroundgap')->limit(10),
                TextColumn::make('acrossgap')->limit(10),
                TextColumn::make('cylinder.teeth')->limit(10),
                TextColumn::make('cornerradius')->limit(10),
                TextColumn::make('media')->limit(50),
                TextColumn::make('customermark')->limit(50),

            ])
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
            'index' => Pages\ListRotarydies::route('/'),
            'create' => Pages\CreateRotarydie::route('/create'),
            'edit' => Pages\EditRotarydie::route('/{record}/edit'),
        ];
    }
}