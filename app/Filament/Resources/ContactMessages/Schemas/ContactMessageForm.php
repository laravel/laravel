<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')->required()->maxLength(255),
                \Filament\Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                \Filament\Forms\Components\TextInput::make('subject')->required()->maxLength(255),
                \Filament\Forms\Components\Textarea::make('message')->required()->rows(5),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'replied' => 'Replied',
                    ])
                    ->required()
                    ->default('new'),
            ]);
    }
}
