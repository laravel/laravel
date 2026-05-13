<?php

namespace App\Filament\Resources\BillingTransactions\Pages;

use App\Filament\Resources\BillingTransactions\BillingTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBillingTransaction extends EditRecord
{
    protected static string $resource = BillingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
