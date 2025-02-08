<?php
// app/Helpers/Helpers.php

/**
 * This is your custom Helpers.php where you can define custom Helper Functions.
 * You can add as many helper functions as you need in this file.
 */

if (! function_exists('currency_format')) {
    /**
     * Format a given numeric value into a locale-aware currency string.
     *
     * @param  float|int  $amount         The numeric amount to format.
     * @param  string     $currencyCode   The ISO 4217 currency code (default: 'USD').
     * @param  string     $locale         The locale identifier (default: 'en_US').
     * @param  int        $fractionDigits The number of decimal digits (default: 2).
     * @return string
     */
    function currency_format($amount, $currencyCode = 'USD', $locale = 'en_US', $fractionDigits = 2)
    {
        // Ensure that we have a numeric value
        if (! is_numeric($amount)) {
            return $amount;
        }
        
        // Use the NumberFormatter class if available for locale-aware formatting
        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fractionDigits);
            $formatted = $formatter->formatCurrency($amount, $currencyCode);
            if ($formatted !== false) {
                return $formatted;
            }
        }
        
        // Fallback: use number_format and prepend the currency code if NumberFormatter is not available
        return $currencyCode . ' ' . number_format($amount, $fractionDigits);
    }
}

// You can add additional custom helper functions below this line.
