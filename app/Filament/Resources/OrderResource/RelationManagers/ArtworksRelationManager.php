<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArtworksRelationManager extends RelationManager
{
    protected static string $relationship = 'artworks';

    protected static ?string $recordTitleAttribute = 'orderno';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('description')->required(),

                Forms\Components\TextInput::make('requiredqty')->required(),
                Forms\Components\TextInput::make('jobrun'),
                Forms\Components\TextInput::make('labelrepeat'),
                Forms\Components\TextInput::make('printedqty'),
                // Forms\Components\TextInput::make('artworks_media_id'),
                Forms\Components\Select::make('awstatus')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'printed' => 'Printed',
                        'platesent' => 'Plate Sent',
                        'sentforapproval' => 'Sent for Approval',
                        'noartworkfile' => 'No Artwork File',
                    ]),
                Forms\Components\TextInput::make('remark'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('requiredqty'),

                Tables\Columns\BadgeColumn::make('awstatus')
                    ->colors([
                        'warning' => 'Pending',
                        'warning' => 'sentforapproval',
                        'success' => 'Approved',
                        'success' => 'Printed',
                        'success' => 'Plate Sent',
                        'warning' => 'noartworkfile',
                    ])->sortable(),
                Tables\Columns\TextColumn::make('remark'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}