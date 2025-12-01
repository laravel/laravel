<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Select::make('type')
                    ->options([
                        'hosting' => 'Hosting',
                        'domain' => 'Domain',
                        'design' => 'Web Design',
                        'seo' => 'SEO',
                    ])
                    ->required(),
                
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                
                DatePicker::make('renewal_date'),
                
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Service name in English')
                    ->columnSpanFull(),
                
                TextInput::make('name.ar')
                    ->label('Name (Arabic - العربية)')
                    ->maxLength(255)
                    ->helperText('Service name in Arabic')
                    ->nullable()
                    ->columnSpanFull(),
                
                Textarea::make('notes.en')
                    ->label('Notes (English)')
                    ->rows(3)
                    ->helperText('Admin notes in English')
                    ->columnSpanFull(),
                
                Textarea::make('notes.ar')
                    ->label('Notes (Arabic - العربية)')
                    ->rows(3)
                    ->helperText('Admin notes in Arabic')
                    ->nullable()
                    ->columnSpanFull(),
                
                \Filament\Forms\Components\KeyValue::make('details')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
