<?php

namespace App\Filament\Resources\RotarydieResource\Pages;

use App\Filament\Resources\RotarydieResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRotarydies extends ListRecords
{
    protected static string $resource = RotarydieResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
