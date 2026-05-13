<?php

namespace App\Filament\Resources\DnsRecords\Pages;

use App\Filament\Resources\DnsRecords\DnsRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDnsRecord extends CreateRecord
{
    protected static string $resource = DnsRecordResource::class;
}
