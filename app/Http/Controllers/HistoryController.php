<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /** Order History page — completed & cancelled orders */
    public function index()
    {
        $userId = Auth::id();

        $completedOrders = Order::with('items.product')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->latest()
            ->get();

        $cancelledOrders = Order::with('items.product')
            ->where('user_id', $userId)
            ->where('status', 'cancelled')
            ->latest()
            ->get();

        $unreadNotifications = UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return view('customer.history', compact(
            'completedOrders',
            'cancelledOrders',
            'unreadNotifications'
        ));
    }

    /** Remove a completed or cancelled order from history */
    public function destroy(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Only completed or cancelled orders can be removed from history.');
        }

        $order->delete();

        return back()->with('success', 'Order removed from history.');
    }
}