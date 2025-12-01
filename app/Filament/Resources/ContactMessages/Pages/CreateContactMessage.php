<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactMessage extends CreateRecord
{
    protected static string $resource = ContactMessageResource::class;
}
