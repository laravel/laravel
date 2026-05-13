<?php

namespace App\Filament\Resources\BillingTransactions;

use App\Filament\Resources\BillingTransactions\Pages\CreateBillingTransaction;
use App\Filament\Resources\BillingTransactions\Pages\EditBillingTransaction;
use App\Filament\Resources\BillingTransactions\Pages\ListBillingTransactions;
use App\Filament\Resources\BillingTransactions\Schemas\BillingTransactionForm;
use App\Filament\Resources\BillingTransactions\Tables\BillingTransactionsTable;
use App\Models\BillingTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BillingTransactionResource extends Resource
{
    protected static ?string $model = BillingTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public static function form(Schema $schema): Schema
    {
        return BillingTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillingTransactionsTable::configure($table);
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
            'index' => ListBillingTransactions::route('/'),
            'create' => CreateBillingTransaction::route('/create'),
            'edit' => EditBillingTransaction::route('/{record}/edit'),
        ];
    }
}
