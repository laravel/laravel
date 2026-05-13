<?php

namespace App\Filament\Resources\BillingTransactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BillingTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Select::make('type')
                    ->options(['deposit' => 'Deposit', 'payment' => 'Payment', 'refund' => 'Refund'])
                    ->required(),
                TextInput::make('description')
                    ->required(),
                TextInput::make('payment_method'),
                TextInput::make('transaction_id'),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'completed' => 'Completed', 'failed' => 'Failed'])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
