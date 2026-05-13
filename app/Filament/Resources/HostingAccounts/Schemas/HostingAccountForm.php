<?php

namespace App\Filament\Resources\HostingAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HostingAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('domain_id')
                    ->relationship('domain', 'id')
                    ->required(),
                TextInput::make('cpanel_username')
                    ->required(),
                TextInput::make('cpanel_password')
                    ->password(),
                Select::make('status')
                    ->options([
            'active' => 'Active',
            'suspended' => 'Suspended',
            'pending_setup' => 'Pending setup',
            'terminated' => 'Terminated',
        ])
                    ->default('pending_setup')
                    ->required(),
                TextInput::make('plan_name'),
                TextInput::make('server_ip'),
            ]);
    }
}
