<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /** Show the cart page */
   public function index()
{
    $cartItems = CartItem::with('product')
        ->where('user_id', Auth::id())
        ->get();

    // Get only the products that are in the cart
    $productIds = $cartItems->pluck('product_id');
    $products = Product::whereIn('id', $productIds)->get();

    $unreadNotifications = UserNotification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->count();

    return view('customer.cart', compact('cartItems', 'products', 'unreadNotifications'));
}

    /** Add item or update quantity if already in cart */
    public function addOrUpdate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.5',
        ]);

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id,
            ],
            ['quantity' => $request->quantity]
        );

        $cartItem->load('product');

        return response()->json([
            'success'    => true,
            'cart_item'  => $cartItem,
            'cart_count' => CartItem::where('user_id', Auth::id())->count(),
        ]);
    }

    /** Update quantity of a specific cart item */
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['quantity' => 'required|numeric|min:0.5']);

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['success' => true]);
    }

    /** Remove a single item from cart */
    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success'    => true,
            'cart_count' => CartItem::where('user_id', Auth::id())->count(),
        ]);
    }

    /** Clear the entire cart */
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true]);
    }

    /** Return cart item count (for badge) */
    public function count()
    {
        $count = CartItem::where('user_id', Auth::id())->count();

        return response()->json(['count' => $count]);
    }
}