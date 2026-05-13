<?php

namespace App\Filament\Resources\DnsRecords\Pages;

use App\Filament\Resources\DnsRecords\DnsRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDnsRecord extends EditRecord
{
    protected static string $resource = DnsRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
