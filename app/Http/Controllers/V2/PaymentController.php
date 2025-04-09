<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * عرض صفحة الدفع
     */
    public function showPaymentPage(Request $request, $quoteId)
    {
        if (!config('v2_features.payment_system.enabled')) {
            return redirect()->back()->with('error', __('v2.feature_disabled'));
        }

        $quote = Quote::with(['request', 'subagent'])->findOrFail($quoteId);
        
        // التحقق من أن المستخدم مخول بالدفع
        $this->authorize('pay', $quote);
        
        $paymentGateways = $this->getAvailablePaymentGateways();
        
        return view('v2.payments.checkout', compact('quote', 'paymentGateways'));
    }
    
    /**
     * معالجة طلب الدفع
     */
    public function processPayment(Request $request, $quoteId)
    {
        if (!config('v2_features.payment_system.enabled')) {
            return redirect()->back()->with('error', __('v2.feature_disabled'));
        }

        $quote = Quote::findOrFail($quoteId);
        
        // التحقق من أن المستخدم مخول بالدفع
        $this->authorize('pay', $quote);
        
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency_code' => 'required|string|size:3',
            'card_number' => 'required_if:payment_method,credit_card',
            'card_holder' => 'required_if:payment_method,credit_card',
            'expiry_date' => 'required_if:payment_method,credit_card',
            'cvv' => 'required_if:payment_method,credit_card',
        ]);
        
        // إنشاء سجل الدفع
        $payment = new Payment([
            'payment_id' => Str::uuid(),
            'amount' => $validated['amount'],
            'currency_code' => $validated['currency_code'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'quote_id' => $quote->id,
            'user_id' => auth()->id(),
        ]);
        
        // معالجة الدفع عبر البوابة المختارة
        try {
            $paymentResult = $this->processPaymentWithGateway(
                $validated['payment_method'],
                $validated,
                $quote,
                $payment
            );
            
            if ($paymentResult['success']) {
                // تحديث حالة الدفع والاقتباس
                $payment->status = 'completed';
                $payment->transaction_id = $paymentResult['transaction_id'];
                $payment->save();
                
                $quote->status = 'paid';
                $quote->save();
                
                return redirect()
                    ->route('v2.payments.success', ['payment' => $payment->id])
                    ->with('success', __('v2.payment_success'));
            } else {
                // تحديث حالة الدفع بالفشل
                $payment->status = 'failed';
                $payment->error_message = $paymentResult['error_message'] ?? __('v2.payment_failed');
                $payment->save();
                
                return redirect()
                    ->route('v2.payments.failed', ['payment' => $payment->id])
                    ->with('error', $paymentResult['error_message'] ?? __('v2.payment_failed'));
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            
            // تسجيل الخطأ
            $payment->status = 'failed';
            $payment->error_message = $e->getMessage();
            $payment->save();
            
            return redirect()
                ->back()
                ->with('error', __('v2.payment_failed') . ': ' . $e->getMessage());
        }
    }
    
    /**
     * عرض صفحة نجاح الدفع
     */
    public function showSuccessPage(Payment $payment)
    {
        $this->authorize('view', $payment);
        
        return view('v2.payments.success', compact('payment'));
    }
    
    /**
     * عرض صفحة فشل الدفع
     */
    public function showFailedPage(Payment $payment)
    {
        $this->authorize('view', $payment);
        
        return view('v2.payments.failed', compact('payment'));
    }
    
    /**
     * الحصول على بوابات الدفع المتاحة
     */
    private function getAvailablePaymentGateways()
    {
        $gateways = [];
        $config = config('v2_features.payment_system.gateways');
        
        if ($config['mada']) {
            $gateways['mada'] = [
                'name' => 'مدى',
                'icon' => 'mada-icon.svg',
            ];
        }
        
        if ($config['visa']) {
            $gateways['visa'] = [
                'name' => 'Visa',
                'icon' => 'visa-icon.svg',
            ];
        }
        
        if ($config['mastercard']) {
            $gateways['mastercard'] = [
                'name' => 'Mastercard',
                'icon' => 'mastercard-icon.svg',
            ];
        }
        
        if ($config['apple_pay']) {
            $gateways['apple_pay'] = [
                'name' => 'Apple Pay',
                'icon' => 'apple-pay-icon.svg',
            ];
        }
        
        if ($config['google_pay']) {
            $gateways['google_pay'] = [
                'name' => 'Google Pay',
                'icon' => 'google-pay-icon.svg',
            ];
        }
        
        return $gateways;
    }
    
    /**
     * معالجة الدفع عبر البوابة المحددة
     */
    private function processPaymentWithGateway($gateway, $paymentData, $quote, $payment)
    {
        // في وضع الاختبار، إرجاع نجاح افتراضي
        if (config('v2_features.payment_system.test_mode')) {
            return [
                'success' => true,
                'transaction_id' => 'TEST_' . Str::random(16),
                'message' => 'Test payment processed successfully',
            ];
        }
        
        // في الإنتاج، اتصل ببوابة الدفع الفعلية
        switch ($gateway) {
            case 'mada':
                return $this->processMadaPayment($paymentData, $quote, $payment);
            case 'visa':
            case 'mastercard':
                return $this->processCreditCardPayment($paymentData, $quote, $payment);
            case 'apple_pay':
                return $this->processApplePayPayment($paymentData, $quote, $payment);
            case 'google_pay':
                return $this->processGooglePayPayment($paymentData, $quote, $payment);
            default:
                throw new \Exception(__('v2.invalid_payment_method'));
        }
    }
    
    /**
     * معالجة الدفع عبر مدى
     */
    private function processMadaPayment($paymentData, $quote, $payment)
    {
        // تنفيذ منطق الاتصال ببوابة مدى
        // هذا نموذج سيتم استبداله بالتكامل الفعلي
        
        return [
            'success' => true,
            'transaction_id' => 'MADA_' . Str::random(16),
            'message' => 'Mada payment processed successfully',
        ];
    }
    
    /**
     * معالجة الدفع ببطاقة الائتمان
     */
    private function processCreditCardPayment($paymentData, $quote, $payment)
    {
        // تنفيذ منطق الاتصال ببوابة بطاقات الائتمان
        // هذا نموذج سيتم استبداله بالتكامل الفعلي
        
        return [
            'success' => true,
            'transaction_id' => 'CC_' . Str::random(16),
            'message' => 'Credit card payment processed successfully',
        ];
    }
    
    /**
     * معالجة الدفع عبر Apple Pay
     */
    private function processApplePayPayment($paymentData, $quote, $payment)
    {
        // تنفيذ منطق الاتصال ببوابة Apple Pay
        // هذا نموذج سيتم استبداله بالتكامل الفعلي
        
        return [
            'success' => true,
            'transaction_id' => 'APPLEPAY_' . Str::random(16),
            'message' => 'Apple Pay payment processed successfully',
        ];
    }
    
    /**
     * معالجة الدفع عبر Google Pay
     */
    private function processGooglePayPayment($paymentData, $quote, $payment)
    {
        // تنفيذ منطق الاتصال ببوابة Google Pay
        // هذا نموذج سيتم استبداله بالتكامل الفعلي
        
        return [
            'success' => true,
            'transaction_id' => 'GOOGLEPAY_' . Str::random(16),
            'message' => 'Google Pay payment processed successfully',
        ];
    }
}
