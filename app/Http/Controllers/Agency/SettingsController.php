<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use App\Models\Currency;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                return $this->updateAgencyInfo($request, $agency);
            
            case 'currency_settings':
                return $this->updateCurrencySettings($request, $agency);

            case 'notification_settings':
                return $this->updateNotificationSettings($request, $agency);

            case 'email_settings':
                return $this->updateEmailSettings($request, $agency);

            case 'commission_settings':
                return $this->updateCommissionSettings($request, $agency);
                
            case 'integration_settings':
                return $this->updateIntegrationSettings($request, $agency);
            
            default:
                return redirect()->back()->with('error', 'نوع الإعدادات غير صالح');
        }
    }

    /**
     * عرض معلومات النظام
     *
     * @return \Illuminate\View\View
     */
    public function systemInfo()
    {
        return view('agency.settings.system_info');
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
            // حذف الشعار القديم إذا وجد
            if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
                Storage::disk('public')->delete($agency->logo_path);
            }
            
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
        }

        return redirect()->back()->with('success', 'تم تحديث معلومات الوكالة بنجاح');
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

        return redirect()->back()->with('success', 'تم تحديث إعدادات العملات بنجاح');
    }

    /**
     * تحديث إعدادات الإشعارات
     */
    private function updateNotificationSettings(Request $request, Agency $agency)
    {
        $request->validate([
            'enable_email_notifications' => 'nullable|boolean',
            'enable_system_notifications' => 'nullable|boolean',
            'notify_on_new_request' => 'nullable|boolean',
            'notify_on_new_quote' => 'nullable|boolean',
            'notify_on_status_change' => 'nullable|boolean',
            'daily_summary' => 'nullable|boolean',
            'notify_customers' => 'nullable|boolean',
            'notify_subagents' => 'nullable|boolean',
        ]);

        $data = [
            'notification_settings' => [
                'enable_email_notifications' => $request->has('enable_email_notifications'),
                'enable_system_notifications' => $request->has('enable_system_notifications'),
                'notify_on_new_request' => $request->has('notify_on_new_request'),
                'notify_on_new_quote' => $request->has('notify_on_new_quote'),
                'notify_on_status_change' => $request->has('notify_on_status_change'),
                'daily_summary' => $request->has('daily_summary'),
                'notify_customers' => $request->has('notify_customers'),
                'notify_subagents' => $request->has('notify_subagents'),
            ]
        ];

        $agency->update($data);

        return redirect()->back()->with('success', 'تم تحديث إعدادات الإشعارات بنجاح');
    }

    /**
     * تحديث إعدادات البريد الإلكتروني
     */
    private function updateEmailSettings(Request $request, Agency $agency)
    {
        $request->validate([
            'email_sender_name' => 'required|string|max:255',
            'email_sender_address' => 'required|email|max:255',
            'email_template' => 'required|in:default,minimal,branded',
            'email_signature' => 'nullable|string',
            'email_footer_text' => 'nullable|string',
        ]);

        $data = [
            'email_settings' => [
                'sender_name' => $request->email_sender_name,
                'sender_address' => $request->email_sender_address,
                'template' => $request->email_template,
                'signature' => $request->email_signature,
                'footer_text' => $request->email_footer_text,
            ]
        ];

        $agency->update($data);

        return redirect()->back()->with('success', 'تم تحديث إعدادات البريد الإلكتروني بنجاح');
    }

    /**
     * تحديث إعدادات العمولات
     */
    private function updateCommissionSettings(Request $request, Agency $agency)
    {
        $request->validate([
            'default_commission_rate' => 'required|numeric|min:0|max:100',
            'minimum_commission_amount' => 'nullable|numeric|min:0',
            'commission_calculation_method' => 'required|in:percentage,fixed,tiered',
            'auto_calculate_commission' => 'nullable|boolean',
            'apply_commission_tax' => 'nullable|boolean',
            'commission_tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = [
            'default_commission_rate' => $request->default_commission_rate,
            'commission_settings' => [
                'minimum_amount' => $request->minimum_commission_amount,
                'calculation_method' => $request->commission_calculation_method,
                'auto_calculate' => $request->has('auto_calculate_commission'),
                'apply_tax' => $request->has('apply_commission_tax'),
                'tax_rate' => $request->commission_tax_rate,
            ]
        ];

        $agency->update($data);

        return redirect()->back()->with('success', 'تم تحديث إعدادات العمولات بنجاح');
    }

    /**
     * تحديث إعدادات التكامل
     */
    private function updateIntegrationSettings(Request $request, Agency $agency)
    {
        $request->validate([
            'enable_paypal' => 'nullable|boolean',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
            'paypal_sandbox' => 'nullable|boolean',
            'enable_stripe' => 'nullable|boolean',
            'stripe_publishable_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
            'enable_google_maps' => 'nullable|boolean',
            'google_maps_api_key' => 'nullable|string',
            'enable_currency_api' => 'nullable|boolean',
            'currency_api_source' => 'nullable|in:openexchangerates,currencylayer,fixer',
            'currency_api_key' => 'nullable|string',
            'currency_update_frequency' => 'nullable|in:daily,weekly,monthly',
            'enable_api_access' => 'nullable|boolean',
        ]);

        $data = [
            'integration_settings' => [
                'enable_paypal' => $request->has('enable_paypal'),
                'paypal_client_id' => $request->paypal_client_id,
                'paypal_secret' => $request->paypal_secret,
                'paypal_sandbox' => $request->has('paypal_sandbox'),
                'enable_stripe' => $request->has('enable_stripe'),
                'stripe_publishable_key' => $request->stripe_publishable_key,
                'stripe_secret_key' => $request->stripe_secret_key,
                'stripe_webhook_secret' => $request->stripe_webhook_secret,
                'enable_google_maps' => $request->has('enable_google_maps'),
                'google_maps_api_key' => $request->google_maps_api_key,
                'enable_currency_api' => $request->has('enable_currency_api'),
                'currency_api_source' => $request->currency_api_source,
                'currency_api_key' => $request->currency_api_key,
                'currency_update_frequency' => $request->currency_update_frequency,
                'enable_api_access' => $request->has('enable_api_access'),
            ]
        ];

        // إنشاء مفتاح API إذا تم تفعيل الوصول للـ API
        if ($request->has('enable_api_access') && !$agency->api_key) {
            $data['api_key'] = Str::random(64);
        }

        $agency->update($data);

        return redirect()->back()->with('success', 'تم تحديث إعدادات التكامل بنجاح');
    }
}
