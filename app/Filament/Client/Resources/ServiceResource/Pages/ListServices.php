<?php

namespace App\Filament\Client\Resources\ServiceResource\Pages;

use App\Filament\Client\Resources\ServiceResource;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for clients
        ];
    }
}
