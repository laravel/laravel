<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::where('is_available', true)->get();

        $cartCount = CartItem::where('user_id', Auth::id())->count();

        $unreadNotifications = UserNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('customer.shop', compact('products', 'cartCount', 'unreadNotifications'));
    }
}