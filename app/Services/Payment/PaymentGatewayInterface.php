<?php

namespace App\Services\Payment;

use App\Models\Invoice;

interface PaymentGatewayInterface
{
    /**
     * تنفيذ عملية الدفع
     *
     * @param Invoice $invoice
     * @param array $paymentData
     * @return array
     */
    public function charge(Invoice $invoice, array $paymentData): array;
    
    /**
     * التحقق من حالة المعاملة
     *
     * @param string $transactionId
     * @return array
     */
    public function checkStatus(string $transactionId): array;
    
    /**
     * استرداد الدفعة
     *
     * @param string $transactionId
     * @param float|null $amount
     * @return array
     */
    public function refund(string $transactionId, ?float $amount = null): array;
}
