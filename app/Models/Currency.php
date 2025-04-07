<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'is_default',
        'exchange_rate',
        'is_active'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'exchange_rate' => 'float',
    ];

    /**
     * Get the default currency
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->first() ?? self::where('code', 'SAR')->first();
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $from = is_string($fromCurrency) ? self::where('code', $fromCurrency)->first() : $fromCurrency;
        $to = is_string($toCurrency) ? self::where('code', $toCurrency)->first() : $toCurrency;

        if (!$from || !$to) {
            return $amount;
        }

        // Convert to base currency first (the one with exchange_rate = 1)
        $baseAmount = $amount / $from->exchange_rate;
        
        // Then convert to target currency
        return $baseAmount * $to->exchange_rate;
    }

    /**
     * Format amount with currency symbol
     */
    public static function format($amount, $currencyCode = null)
    {
        if (!$currencyCode) {
            $currency = self::getDefault();
        } else {
            $currency = self::where('code', $currencyCode)->first() ?? self::getDefault();
        }

        return number_format($amount, 2) . ' ' . $currency->symbol;
    }
}
