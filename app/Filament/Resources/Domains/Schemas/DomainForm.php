<?php

namespace App\Filament\Resources\Domains\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DomainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('domain_name')
                    ->required(),
                TextInput::make('parent_domain')
                    ->required(),
                Select::make('status')
                    ->options([
            'active' => 'Active',
            'suspended' => 'Suspended',
            'pending' => 'Pending',
            'expired' => 'Expired',
        ])
                    ->default('active')
                    ->required(),
                TextInput::make('cloudflare_zone_id'),
                DateTimePicker::make('expires_at'),
            ]);
    }
}
