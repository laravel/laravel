<?php

namespace App\Filament\Resources\LandingPageContents\Pages;

use App\Filament\Resources\LandingPageContents\LandingPageContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLandingPageContents extends ListRecords
{
    protected static string $resource = LandingPageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
