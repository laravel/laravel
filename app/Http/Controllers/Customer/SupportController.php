<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;

class SupportController extends Controller
{
    /**
     * عرض صفحة الدعم الفني
     */
    public function index()
    {
        return view('customer.support.index');
    }
    
    /**
     * إرسال طلب دعم جديد
     */
    public function submit(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'request_id' => 'nullable|exists:requests,id',
        ]);
        
        // للتبسيط، سنقوم فقط بعرض رسالة نجاح
        // في التطبيق الحقيقي، يمكننا إنشاء تذكرة دعم هنا
        
        return redirect()->back()->with('success', 'تم إرسال استفسارك بنجاح. سنقوم بالرد عليك في أقرب وقت ممكن.');
    }
}
