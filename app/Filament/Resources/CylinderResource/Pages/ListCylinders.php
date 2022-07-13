<?php

namespace App\Filament\Resources\CylinderResource\Pages;

use App\Filament\Resources\CylinderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCylinders extends ListRecords
{
    protected static string $resource = CylinderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
