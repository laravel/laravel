<?php

namespace App\Helpers;

use App\Models\Currency;
use App\Models\Agency;

class CurrencyHelper
{
    /**
     * تنسيق سعر حسب إعدادات الوكالة
     *
     * @param float $amount المبلغ المراد تنسيقه
     * @param string|null $currencyCode رمز العملة (إذا كان مختلفاً عن العملة الافتراضية)
     * @param Agency|null $agency الوكالة (لتطبيق إعداداتها)
     * @return string السعر المنسق
     */
    public static function formatPrice($amount, $currencyCode = null, $agency = null)
    {
        // الحصول على الوكالة إذا لم يتم تمريرها
        if (!$agency && auth()->check() && auth()->user()->agency_id) {
            $agency = auth()->user()->agency;
        }

        // تهيئة القيم الافتراضية إذا لم يتم العثور على الوكالة
        $decimals = $agency->price_decimals ?? 2;
        $format = $agency->price_display_format ?? 'symbol_first';

        // الحصول على العملة
        $currency = null;
        
        if (!empty($currencyCode)) {
            // محاولة العثور على العملة بالرمز المحدد
            $currency = Currency::where('code', $currencyCode)->first();
        }
        
        if (!$currency) {
            // إذا لم يتم تحديد عملة أو لم يتم العثور عليها، استخدم العملة الافتراضية للوكالة
            if ($agency && !empty($agency->default_currency)) {
                $currency = Currency::where('code', $agency->default_currency)->first();
            }
            
            // إذا لم يتم العثور على عملة الوكالة، استخدم العملة الافتراضية للنظام
            if (!$currency) {
                $currency = Currency::where('is_default', true)->first();
            }
            
            // إذا لم يتم العثور على أي عملة، قم بإنشاء كائن عملة افتراضي
            if (!$currency) {
                $currency = new Currency([
                    'code' => 'SAR',
                    'symbol' => 'ر.س',
                    'is_default' => true,
                    'exchange_rate' => 1.0000
                ]);
            }
        }

        // تنسيق المبلغ حسب عدد الخانات العشرية
        $formattedAmount = number_format($amount, $decimals);

        // تطبيق تنسيق عرض العملة
        switch ($format) {
            case 'symbol_first':
                return $currency->symbol . ' ' . $formattedAmount;
            case 'symbol_last':
                return $formattedAmount . ' ' . $currency->symbol;
            case 'code_first':
                return $currency->code . ' ' . $formattedAmount;
            case 'code_last':
                return $formattedAmount . ' ' . $currency->code;
            default:
                return $formattedAmount . ' ' . $currency->symbol;
        }
    }

    /**
     * تحويل مبلغ من عملة إلى أخرى
     *
     * @param float $amount المبلغ المراد تحويله
     * @param string $fromCurrency رمز العملة المصدر
     * @param string $toCurrency رمز العملة الهدف
     * @return float المبلغ بعد التحويل
     */
    public static function convertPrice($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $sourceCurrency = Currency::where('code', $fromCurrency)->first();
        $targetCurrency = Currency::where('code', $toCurrency)->first();

        // إذا لم يتم العثور على إحدى العملات، أرجع المبلغ الأصلي
        if (!$sourceCurrency || !$targetCurrency) {
            return $amount;
        }

        // تحويل المبلغ إلى العملة الأساسية (التي لها سعر صرف = 1)
        $baseAmount = $amount / $sourceCurrency->exchange_rate;
        
        // ثم تحويله إلى العملة المستهدفة
        return $baseAmount * $targetCurrency->exchange_rate;
    }
}
