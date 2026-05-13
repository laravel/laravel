<?php

namespace App\Filament\Resources\HostingAccounts;

use App\Filament\Resources\HostingAccounts\Pages\CreateHostingAccount;
use App\Filament\Resources\HostingAccounts\Pages\EditHostingAccount;
use App\Filament\Resources\HostingAccounts\Pages\ListHostingAccounts;
use App\Filament\Resources\HostingAccounts\Schemas\HostingAccountForm;
use App\Filament\Resources\HostingAccounts\Tables\HostingAccountsTable;
use App\Models\HostingAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HostingAccountResource extends Resource
{
    protected static ?string $model = HostingAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'username';

    public static function form(Schema $schema): Schema
    {
        return HostingAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HostingAccountsTable::configure($table);
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
            'index' => ListHostingAccounts::route('/'),
            'create' => CreateHostingAccount::route('/create'),
            'edit' => EditHostingAccount::route('/{record}/edit'),
        ];
    }
}
