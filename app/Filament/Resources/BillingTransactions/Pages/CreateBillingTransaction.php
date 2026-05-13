<?php

namespace App\Filament\Resources\BillingTransactions\Pages;

use App\Filament\Resources\BillingTransactions\BillingTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBillingTransaction extends CreateRecord
{
    protected static string $resource = BillingTransactionResource::class;
}
