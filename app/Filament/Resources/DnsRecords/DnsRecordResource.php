<?php

namespace App\Filament\Resources\DnsRecords;

use App\Filament\Resources\DnsRecords\Pages\CreateDnsRecord;
use App\Filament\Resources\DnsRecords\Pages\EditDnsRecord;
use App\Filament\Resources\DnsRecords\Pages\ListDnsRecords;
use App\Filament\Resources\DnsRecords\Schemas\DnsRecordForm;
use App\Filament\Resources\DnsRecords\Tables\DnsRecordsTable;
use App\Models\DnsRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DnsRecordResource extends Resource
{
    protected static ?string $model = DnsRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DnsRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DnsRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDnsRecords::route('/'),
            'create' => CreateDnsRecord::route('/create'),
            'edit' => EditDnsRecord::route('/{record}/edit'),
        ];
    }
}
