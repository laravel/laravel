<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * عرض قائمة الإشعارات
     */
    public function index()
    {
        // الحصول على كل الإشعارات
        $notifications = Auth::user()->notifications()->paginate(15);
        
        return view('customer.notifications.index', compact('notifications'));
    }

    /**
     * تحديد الإشعارات كمقروءة
     */
    public function markRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string',
        ]);

        Auth::user()->notifications()
                   ->whereIn('id', $request->ids)
                   ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * حذف إشعار معين
     */
    public function destroy($id)
    {
        Auth::user()->notifications()->where('id', $id)->delete();
        
        return redirect()->route('customer.notifications.index')
                        ->with('success', 'تم حذف الإشعار بنجاح');
    }
}
