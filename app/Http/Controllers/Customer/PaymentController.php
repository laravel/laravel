<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * عرض صفحة الدفع للفاتورة
     */
    public function showPaymentPage(Invoice $invoice)
    {
        // التحقق من أن الفاتورة تنتمي للعميل
        if ($invoice->user_id !== auth()->id()) {
            abort(403, __('messages.access_denied'));
        }
        
        // التحقق من أن الفاتورة غير مدفوعة
        if ($invoice->status === 'paid') {
            return redirect()->route('customer.invoices.show', $invoice)
                           ->with('info', __('messages.invoice_already_paid'));
        }
        
        $invoice->load(['items']);
        
        // الحصول على بوابات الدفع المتاحة
        $paymentGateways = config('payment.enabled_gateways', ['stripe', 'paypal']);
        
        return view('customer.payments.pay', compact('invoice', 'paymentGateways'));
    }
    
    /**
     * معالجة عملية الدفع
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        // التحقق من أن الفاتورة تنتمي للعميل
        if ($invoice->user_id !== auth()->id()) {
            abort(403, __('messages.access_denied'));
        }
        
        $request->validate([
            'payment_gateway' => 'required|string|in:' . implode(',', config('payment.enabled_gateways', [])),
            'payment_method' => 'required|string',
            'card_number' => 'required_if:payment_method,credit_card',
            'card_holder' => 'required_if:payment_method,credit_card',
            'exp_month' => 'required_if:payment_method,credit_card|numeric|min:1|max:12',
            'exp_year' => 'required_if:payment_method,credit_card|numeric|min:' . date('Y'),
            'cvv' => 'required_if:payment_method,credit_card|numeric',
        ]);
        
        try {
            $paymentManager = new PaymentGatewayManager();
            
            $paymentData = $request->only([
                'payment_method',
                'card_number',
                'card_holder',
                'exp_month',
                'exp_year',
                'cvv',
            ]);
            
            // معالجة الدفع عبر البوابة المحددة
            $payment = $paymentManager->processPayment(
                $invoice,
                $request->payment_gateway,
                $paymentData
            );
            
            if ($payment->status === 'completed') {
                return redirect()->route('customer.invoices.show', $invoice)
                               ->with('success', __('messages.payment_successful'));
            } else {
                return redirect()->route('customer.payments.show', $invoice)
                               ->with('error', __('messages.payment_failed'));
            }
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()
                        ->with('error', __('messages.payment_processing_error'));
        }
    }
}
