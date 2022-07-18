<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CylinderResource\Pages;
use App\Filament\Resources\CylinderResource\RelationManagers;
use App\Models\Cylinder;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;




class CylinderResource extends Resource
{
    protected static ?string $model = Cylinder::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Rotary Die';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                TextInput::make('teeth')->numeric(),
                TextInput::make('circumference_mm')->numeric(),
                TextInput::make('circumference_inch')->numeric(),
                Toggle::make('machine1'),
                Toggle::make('machine2'),

                


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teeth')
                                ->searchable()
                ->sortable()
                ->label('Teeth'),
                TextColumn::make('circumference_mm'),
                TextColumn::make('circumference_inch'),
                BooleanColumn::make('machine1'),
                BooleanColumn::make('machine2'),

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
            'index' => Pages\ListCylinders::route('/'),
            'create' => Pages\CreateCylinder::route('/create'),
            'edit' => Pages\EditCylinder::route('/{record}/edit'),
        ];
    }
}
