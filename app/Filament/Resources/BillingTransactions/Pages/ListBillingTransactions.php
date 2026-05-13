<?php

namespace App\Filament\Resources\BillingTransactions\Pages;

use App\Filament\Resources\BillingTransactions\BillingTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBillingTransactions extends ListRecords
{
    protected static string $resource = BillingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
