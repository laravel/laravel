<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use App\Models\Currency;

class SettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        $agency = auth()->user()->agency;
        return view('agency.settings.index', compact('agency'));
    }

    /**
     * تحديث إعدادات الوكالة
     */
    public function update(Request $request)
    {
        $agency = auth()->user()->agency;
        $settingsType = $request->input('settings_type', 'agency_info');

        switch ($settingsType) {
            case 'agency_info':
                $this->updateAgencyInfo($request, $agency);
                break;
            
            case 'currency_settings':
                $this->updateCurrencySettings($request, $agency);
                break;

            case 'commission_settings':
                $this->updateCommissionSettings($request, $agency);
                break;
            
            // يمكن إضافة المزيد من الإعدادات هنا
            
            default:
                return redirect()->back()->with('error', 'نوع الإعدادات غير صالح');
        }

        return redirect()->back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    /**
     * تحديث معلومات الوكالة
     */
    private function updateAgencyInfo(Request $request, Agency $agency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'default_currency' => 'required|exists:currencies,code',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'default_currency', 'phone', 'address']);
        
        if ($request->filled('email')) {
            $data['contact_email'] = $request->email;
        }

        // تحديث الوكالة
        $agency->update($data);

        // معالجة الشعار إذا تم تحميله
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('agency_logos', 'public');
            $agency->update(['logo_path' => $logoPath]);
        }

        // تحديث العملة الافتراضية للنظام إذا كانت مختلفة
        $defaultCurrency = Currency::where('code', $request->default_currency)->first();
        if ($defaultCurrency && !$defaultCurrency->is_default) {
            // إلغاء تعيين العملة الافتراضية السابقة
            Currency::where('is_default', true)->update(['is_default' => false]);
            
            // تعيين العملة الجديدة كافتراضية
            $defaultCurrency->update([
                'is_default' => true,
                'exchange_rate' => 1.0000
            ]);
            
            // تحديث أسعار الصرف (سيتم تنفيذه في نسخة مستقبلية)
        }
    }

    /**
     * تحديث إعدادات العملة
     */
    private function updateCurrencySettings(Request $request, Agency $agency)
    {
        $request->validate([
            'price_decimals' => 'required|integer|min:0|max:3',
            'price_display_format' => 'required|in:symbol_first,symbol_last,code_first,code_last',
            'auto_convert_prices' => 'nullable|boolean',
        ]);

        $data = [
            'price_decimals' => $request->price_decimals,
            'price_display_format' => $request->price_display_format,
            'auto_convert_prices' => $request->has('auto_convert_prices'),
        ];

        $agency->update($data);
    }

    /**
     * تحديث إعدادات العمولات
     */
    private function updateCommissionSettings(Request $request, Agency $agency)
    {
        $request->validate([
            'default_commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $agency->update([
            'default_commission_rate' => $request->default_commission_rate,
        ]);
    }
}
