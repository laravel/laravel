<?php

namespace App\Filament\Resources\DnsRecords\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DnsRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('domain_id')
                    ->relationship('domain', 'id')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('proxied')
                    ->required(),
                TextInput::make('ttl')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('cloudflare_record_id'),
            ]);
    }
}
