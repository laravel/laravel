<?php

namespace App\Filament\Resources\RotarydieResource\Pages;

use App\Filament\Resources\RotarydieResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRotarydie extends EditRecord
{
    protected static string $resource = RotarydieResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
