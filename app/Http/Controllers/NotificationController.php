<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * عرض جميع الإشعارات
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * عرض الإشعارات غير المقروءة كجزء من dropdown
     */
    public function unread()
    {
        $notifications = auth()->user()->notifications()->unread()->latest()->take(5)->get();
        return response()->json([
            'count' => auth()->user()->notifications()->unread()->count(),
            'notifications' => $notifications
        ]);
    }
    
    /**
     * تعليم إشعار كمقروء
     */
    public function markAsRead(Notification $notification)
    {
        // التأكد من أن الإشعار ينتمي للمستخدم الحالي
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'غير مصرح لك بالوصول إلى هذا الإشعار'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * تعليم جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()->unread()->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
}
