<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    /**
     * عرض صفحة إدارة العملات
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('agency.settings.currencies', compact('currencies'));
    }

    /**
     * إضافة عملة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:50',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
        ]);

        Currency::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
            'is_active' => true,
            'is_default' => false,
        ]);

        return redirect()->route('agency.settings.currencies')
                        ->with('success', 'تمت إضافة العملة بنجاح');
    }

    /**
     * تحديث بيانات عملة
     */
    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
        ]);

        $currency->update([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
        ]);

        return redirect()->route('agency.settings.currencies')
                        ->with('success', 'تم تحديث بيانات العملة بنجاح');
    }

    /**
     * تغيير حالة تفعيل/تعطيل العملة
     */
    public function toggleStatus(Currency $currency)
    {
        if ($currency->is_default) {
            return redirect()->route('agency.settings.currencies')
                            ->with('error', 'لا يمكن تعطيل العملة الافتراضية');
        }

        $currency->update([
            'is_active' => !$currency->is_active
        ]);

        $status = $currency->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->route('agency.settings.currencies')
                        ->with('success', "تم $status العملة بنجاح");
    }

    /**
     * تعيين العملة كافتراضية
     */
    public function setAsDefault(Currency $currency)
    {
        // التأكد من أن العملة مفعلة
        if (!$currency->is_active) {
            $currency->update(['is_active' => true]);
        }

        // إلغاء تعيين العملة الافتراضية السابقة
        Currency::where('is_default', true)->update(['is_default' => false]);

        // تعيين العملة الجديدة كافتراضية
        $currency->update(['is_default' => true, 'exchange_rate' => 1.0000]);

        // تحديث أسعار الصرف للعملات الأخرى نسبة للعملة الجديدة
        $this->recalculateExchangeRates($currency);

        return redirect()->route('agency.settings.currencies')
                        ->with('success', "تم تعيين {$currency->name} كعملة افتراضية بنجاح");
    }

    /**
     * إعادة حساب أسعار الصرف للعملات عند تغيير العملة الافتراضية
     */
    private function recalculateExchangeRates(Currency $defaultCurrency)
    {
        // هذه الوظيفة ستقوم بإعادة حساب أسعار صرف العملات الأخرى 
        // نسبة إلى العملة الافتراضية الجديدة
        // سيتم تنفيذها في نسخة مستقبلية
    }

    /**
     * حذف عملة
     */
    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return redirect()->route('agency.settings.currencies')
                            ->with('error', 'لا يمكن حذف العملة الافتراضية');
        }

        // التحقق من عدم استخدام العملة في أي خدمات أو عروض أسعار
        // سيتم تنفيذ هذه الوظيفة في نسخة مستقبلية

        $currency->delete();

        return redirect()->route('agency.settings.currencies')
                        ->with('success', 'تم حذف العملة بنجاح');
    }
}
