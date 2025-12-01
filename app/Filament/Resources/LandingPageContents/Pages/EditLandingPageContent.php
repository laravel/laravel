<?php

namespace App\Filament\Resources\LandingPageContents\Pages;

use App\Filament\Resources\LandingPageContents\LandingPageContentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLandingPageContent extends EditRecord
{
    protected static string $resource = LandingPageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
