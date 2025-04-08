<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * عرض صفحة تحرير الملف الشخصي
     */
    public function edit()
    {
        return view('customer.profile.edit');
    }

    /**
     * تحديث الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $updateType = $request->input('update_type');

        switch ($updateType) {
            case 'personal_info':
                return $this->updatePersonalInfo($request, $user);
            
            case 'contact_info':
                return $this->updateContactInfo($request, $user);
            
            case 'password':
                return $this->updatePassword($request, $user);
            
            case 'preferences':
                return $this->updatePreferences($request, $user);
            
            default:
                return redirect()->back()->with('error', 'نوع التحديث غير صالح');
        }
    }

    /**
     * تحديث المعلومات الشخصية
     */
    private function updatePersonalInfo(Request $request, $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'id_number', 'passport_number', 'nationality']);

        // معالجة الصورة الشخصية إذا تم تحميلها
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا وجدت
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'تم تحديث المعلومات الشخصية بنجاح');
    }

    /**
     * تحديث معلومات الاتصال
     */
    private function updateContactInfo(Request $request, $user)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:50',
        ]);

        $user->update($request->only(['email', 'phone', 'address', 'city', 'country']));

        return redirect()->back()->with('success', 'تم تحديث معلومات الاتصال بنجاح');
    }

    /**
     * تحديث كلمة المرور
     */
    private function updatePassword(Request $request, $user)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * تحديث التفضيلات
     */
    private function updatePreferences(Request $request, $user)
    {
        $request->validate([
            'preferred_currency' => 'nullable|string|exists:currencies,code',
            'notification_preferences' => 'nullable|array',
        ]);

        $data = [
            'preferred_currency' => $request->preferred_currency,
            'notification_preferences' => $request->notification_preferences ?? [],
        ];

        $user->update($data);

        return redirect()->back()->with('success', 'تم تحديث التفضيلات بنجاح');
    }
}
