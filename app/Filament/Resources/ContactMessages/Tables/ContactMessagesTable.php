<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('subject')->limit(40)->searchable(),
                \Filament\Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'new',
                        'warning' => 'read',
                        'success' => 'replied',
                    ]),
                \Filament\Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'replied' => 'Replied',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
