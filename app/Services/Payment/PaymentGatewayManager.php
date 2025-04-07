<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class PaymentGatewayManager
{
    /**
     * الحصول على بوابة الدفع المناسبة
     *
     * @param string $gateway
     * @return PaymentGatewayInterface
     * @throws \Exception
     */
    public function gateway(string $gateway)
    {
        switch ($gateway) {
            case 'stripe':
                return new StripeGateway();
            case 'paypal':
                return new PayPalGateway();
            case 'local':
                return new LocalGateway();
            default:
                throw new \Exception("بوابة الدفع غير مدعومة: {$gateway}");
        }
    }

    /**
     * إنشاء عملية دفع
     *
     * @param Invoice $invoice
     * @param string $gateway
     * @param array $paymentData
     * @return Payment
     */
    public function processPayment(Invoice $invoice, string $gateway, array $paymentData)
    {
        try {
            $gatewayInstance = $this->gateway($gateway);
            
            // معالجة الدفع عبر البوابة
            $response = $gatewayInstance->charge($invoice, $paymentData);
            
            // إنشاء سجل الدفع
            $payment = new Payment([
                'user_id' => $invoice->user_id,
                'request_id' => $invoice->request_id,
                'invoice_id' => $invoice->id,
                'payment_method' => $paymentData['payment_method'],
                'payment_gateway' => $gateway,
                'transaction_id' => $response['transaction_id'] ?? null,
                'amount' => $invoice->total,
                'currency_code' => $invoice->currency_code,
                'status' => $response['status'],
                'gateway_response' => $response,
                'paid_at' => now(),
            ]);
            
            $payment->save();
            
            // تحديث حالة الفاتورة إذا تم الدفع بنجاح
            if ($response['status'] === 'completed') {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
                $invoice->save();
            }
            
            return $payment;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'gateway' => $gateway,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
