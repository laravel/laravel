<?php

namespace App\Filament\Resources\CylinderResource\Pages;

use App\Filament\Resources\CylinderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCylinder extends EditRecord
{
    protected static string $resource = CylinderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
