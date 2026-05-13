<?php

namespace App\Filament\Resources\HostingAccounts\Pages;

use App\Filament\Resources\HostingAccounts\HostingAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHostingAccounts extends ListRecords
{
    protected static string $resource = HostingAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
