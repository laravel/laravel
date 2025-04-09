<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        $agency = Agency::findOrFail(Auth::user()->agency_id);
        $currencies = Currency::where('is_active', true)->get();
        
        return view('agency.settings.index', compact('agency', 'currencies'));
    }

    /**
     * تحديث إعدادات الوكالة
     */
    public function update(Request $request)
    {
        $agency = Agency::findOrFail(Auth::user()->agency_id);
        
        // حسب نوع الإعدادات، قم بالتحديث المناسب
        $settingsType = $request->input('settings_type', 'agency_info');
        
        switch($settingsType) {
            case 'agency_info':
                return $this->updateAgencyInfo($request, $agency);
            case 'payment_settings':
                return $this->updatePaymentSettings($request, $agency);
            case 'notification_settings':
                return $this->updateNotificationSettings($request, $agency);
            default:
                return redirect()->back()->with('error', 'نوع الإعدادات غير صالح');
        }
    }
    
    /**
     * تحديث معلومات الوكالة
     */
    protected function updateAgencyInfo(Request $request, Agency $agency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'tax_number' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_currency' => 'nullable|exists:currencies,code',
            'theme_color' => 'nullable|string|max:20',
            'agency_language' => 'nullable|in:ar,en',
        ]);
        
        $data = $request->only([
            'name', 'phone', 'website', 'tax_number', 'commercial_register',
            'address', 'default_currency', 'theme_color', 'agency_language',
            'social_media_instagram', 'social_media_twitter', 
            'social_media_facebook', 'social_media_linkedin'
        ]);
        
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
        
        return redirect()->route('agency.settings.index')
                        ->with('success', 'تم تحديث معلومات الوكالة بنجاح');
    }
    
    /**
     * تحديث إعدادات الدفع
     */
    protected function updatePaymentSettings(Request $request, Agency $agency)
    {
        $request->validate([
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'payment_methods' => 'nullable|array',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_iban' => 'nullable|string|max:50',
        ]);
        
        $paymentSettings = $agency->payment_settings ?? [];
        
        $paymentSettings = array_merge($paymentSettings, [
            'commission_rate' => $request->commission_rate,
            'payment_methods' => $request->payment_methods,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_iban' => $request->bank_iban,
        ]);
        
        $agency->update([
            'payment_settings' => $paymentSettings
        ]);
        
        return redirect()->route('agency.settings.index')
                        ->with('success', 'تم تحديث إعدادات الدفع بنجاح');
    }
    
    /**
     * تحديث إعدادات الإشعارات
     */
    protected function updateNotificationSettings(Request $request, Agency $agency)
    {
        $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'whatsapp_notifications' => 'nullable|boolean',
            'new_request_notification' => 'nullable|boolean',
            'quote_status_notification' => 'nullable|boolean',
        ]);
        
        $notificationSettings = $agency->notification_settings ?? [];
        
        $notificationSettings = array_merge($notificationSettings, [
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'whatsapp_notifications' => $request->boolean('whatsapp_notifications'),
            'new_request_notification' => $request->boolean('new_request_notification'),
            'quote_status_notification' => $request->boolean('quote_status_notification'),
        ]);
        
        $agency->update([
            'notification_settings' => $notificationSettings
        ]);
        
        return redirect()->route('agency.settings.index')
                        ->with('success', 'تم تحديث إعدادات الإشعارات بنجاح');
    }
}
