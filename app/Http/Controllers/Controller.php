<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Products;
use App\Models\Cart;
use App\Models\Cart_Items;
use Illuminate\Support\Facades\Auth;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function check_quantity($product_id, $quantity)
    {

        $product = Products::find($product_id);


        if ($product->stock_quantity == 0) {
            return 1;
        }

        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if ($cart != null) {

            $cart_items = Cart_Items::where('product_id', $product_id)->where('cart_id', $cart->id)->first();

            if ($cart_items != null) {
                $cart_items = $cart_items->toArray();
                $cart_items_quantity = intval($cart_items['quantity']) + $quantity;

                if ($product->stock_quantity < $cart_items_quantity) {
                    return 2;
                }
            }
        }

      

        if ($product->stock_quantity < $quantity) {
            return 3;
        }
    }
}
