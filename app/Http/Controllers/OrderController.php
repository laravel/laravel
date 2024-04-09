<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Cart_Items;
use App\Models\Products;
use App\Models\Order;
use App\Models\Posts;
use App\Models\Order_items;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Address;

class OrderController extends Controller
{



    public function filter_order_table(Request $request)
    {
        $id = $request->input('full_name');
        $status = $request->input('status');

        $query = Order::leftJoin('address', 'order.address_id', '=', 'address.id')->leftJoin('user', 'order.user_id', '=', 'user.id')
            ->select('order.*', 'address.full_address', 'user.full_name');

        if ($id !== null) {
            $query->where('order.user_id', $id);
        }

        if ($status !== null) {
            $query->where('order.status', $status);
        }

        $data = $query->get();

        return response()->json(['data' => $data]);
    }





    public function getOrders_by_userid($id)
    {


        $data = Order::leftJoin('address', 'order.address_id', '=', 'address.id')->where('order.user_id', $id)
            ->select('order.*', 'address.full_address')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function user_view_page_admin($id)
    {

        $user_id = $id;

        $user_data = User::find($user_id);

        $order_data = Order::where('user_id', $user_id)->get();


        $view =  view('admin.user_view', compact('order_data', 'user_data'));
        return $view->render();
    }



    public function get_order_info($id)
    {

        $order_data = Order::find($id);

        $user_id  = $order_data->user_id;

        $user_data = User::find($user_id);

        $address_data = Address::find($order_data->address_id);

        $order_items_data = Order_Items::where('order_id', $order_data->id)
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('posts', 'products.id', '=', 'posts.product_id')
            ->select('posts.image', 'products.*', 'order_items.product_id as id_product', 'order_items.quantity', 'order_items.total_price as order_item_total_price')
            ->get();



        $view =  view('admin.order_view', compact('order_data', 'user_data', 'address_data', 'order_items_data'));
        return $view->render();
    }


    public function change_payment_status(Request $request){

        $id = $request->input('id');

        $order = Order::find($id);
        $order->payment_status = $request->input('payment_status');
        $order->save();

        return response()->json(['payment_status' => 1]);


    }

    public function change_order_status(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $order = Order::find($id);

        if ($status == 4) {
            $status_reason = $request->input('status_reason_rejected');
            $order->status_reason = $status_reason;
        }

        if ($status == 1 && $order->payment_status != 1) {
            return response()->json(['payment_status' => 0]);
        }

        if ($status == 1 && $order->payment_status == 1) {
            $order->status = $status;
        } else {
            $order->status = $status;
        }

        $order->save();

        return response()->json(['order_status' => 1]);
    }


    public function order_list_page_show()
    {
        return view('order_list');
    }


    public function order_list_page(Request $request)
    {
        $user_id = Auth::user()->id;

        $offset = $request->input('offset', 0);
        $orders = Order::where('user_id', $user_id)->orderBy('order_date', 'DESC')->skip($offset)->take(4)->get();

        $orders_total = Order::where('user_id', $user_id)->orderBy('order_date', 'DESC')->get();

        $total_orders = count($orders_total);


        $order_list = view('order_list_render_view', compact('orders'))->render();

        return response()->json(['order_list' => $order_list, 'total_orders' => $total_orders]);
    }

    public function Orders_detail_page($id)
    {


        $order = Order::where('id', $id)->first();



        $order_items = Order_items::where('order_id', $id)
            ->leftJoin('posts', 'order_items.product_id', '=', 'posts.product_id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.*', 'posts.image', 'products.name')
            ->get();


        $address = Address::where('id', $order->address_id)->first();

        return view('order_view')->with(['order' => $order, 'order_items' => $order_items, 'order_address' => $address]);
    }



    public function generateOrderNumber()
    {
        // Get current date in the specified format
        $currentDate = Carbon::now()->format('Y_M_d');

        // Get the count of orders for the user on the current date
        $orderCount = Order::where('order_number', 'like', "ord_{$currentDate}%")->count();

        // Generate order number with leading zeroes
        return "ord_{$currentDate}_" . str_pad($orderCount + 1, 3, '0', STR_PAD_LEFT);
    }

    // this method takes all data from cart and add to orders 
    public function final_order_process()
    {
        $user_id = Auth::user()->id;
        // $user_id = 7;

        $cart = Cart::where('user_id', $user_id)->first();
        $cart_items  = Cart_Items::where('cart_id', $cart->id)->get();

        // return response()->json(['cart_items' => $cart_items]);

        $order_number = $this->generateOrderNumber();
        $a_id = session()->get('address_id');

        $order = new Order();
        $order->user_id = $user_id;
        $order->order_number = $order_number;
        $order->price = $cart->price;
        $order->status = 0;
        $order->number_of_items = $cart->number_of_items;
        $order->sub_total = $cart->sub_total;
        $order->address_id = $a_id;
        $order->order_date = DB::raw('now()');
        $order->discount_amount = $cart->discount_amount;
        $order->save();

        $order_id = $order->id;




        foreach ($cart_items as $item) {
            $order_items = new Order_items();
            $order_items->order_id = $order_id;
            $order_items->product_id = $item->product_id;
            $order_items->product_price = $item->product_price;
            $order_items->sub_total = $item->sub_total;
            $order_items->quantity = $item->quantity;
            $order_items->total_price = $item->total_price;
            $order_items->discount_amount = $item->discount_amount;
            $order_items->save();

            $product = Products::find($item->product_id);
            $product->stock_quantity -= $item->quantity;
            $product->save();
        }

        $cart->delete();
        $cart_items->each->delete();

        session()->put('cart_total_items', 0);



        return response()->json(['order_status' => 1]);
    }





    public function cart_checkout()
    {

        $id = Auth::user()->id;

        $cart = Cart::where('user_id', $id)->first();
        $cart_items = Cart_Items::where('cart_id', $cart->id)
            ->leftJoin('posts', 'cart_items.product_id', '=', 'posts.product_id')
            ->leftJoin('products', 'cart_items.product_id', '=', 'products.id')
            ->select('cart_items.*', 'posts.image', 'products.name')
            ->get();

        //   return response()->json(['cart' => $cart, 'cart_items' => $cart_items]);

        return view('cart_checkout_page')->with(['cart' => $cart, 'cart_items' => $cart_items]);
    }


    public function set_Address_order(Request $request)
    {
        $value = $request->input('value');
        $address = Address::where('id', $value)->first();
        session()->put('user_address', $address->full_address);
        session()->put('address_id', $value);
        return response()->json(['status_changed' => 1]);
    }

    // place order page view with data
    public function place_order_confirmation()
    {

        $id = Auth::user()->id;

        $cart = Cart::where('user_id', $id)->first();
        $cart_items = Cart_Items::where('cart_id', $cart->id)
            ->leftJoin('posts', 'cart_items.product_id', '=', 'posts.product_id')
            ->leftJoin('products', 'cart_items.product_id', '=', 'products.id')
            ->select('cart_items.*', 'posts.image', 'products.name')
            ->get();

        $user_data = User::find($id);

        $user_address = Address::where('user_id', $id)->first();

        if (!session()->has('user_address')) {
            session()->put('user_address', $user_address->full_address);
            $address_id = Address::select('id')->where("user_id", $id)->first();
            session()->put('address_id', $address_id->id);
        }



        return view('place_order')->with(['cart' => $cart, 'cart_items' => $cart_items, 'user_data' => $user_data]);
    }



    public function decrement_item_checkout_page(Request $request)
    {
        $id = $request->input('id');


        $cart_item_data = Cart_Items::select('cart_items.*', 'products.*')
            ->leftJoin('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.id', $id)
            ->first();


        $user_id = Auth::user()->id;
        $cart = Cart::where('user_id', $user_id)->first();


        if ($cart_item_data->quantity > 1) {

            $cart->price -= $cart_item_data->product_price;
            $cart->number_of_items -= 1;
            $cart->sub_total -= $cart_item_data->product_price - $cart_item_data->discount_amount;
            $cart->discount_amount -= $cart_item_data->discount_amount;
            $cart->save();

            $cart_item = Cart_Items::find($id);
            $cart_item->quantity -= 1;
            $cart_item->total_price -= $cart_item_data->product_price;
            $cart_item->sub_total -= $cart_item_data->product_price - $cart_item_data->discount_amount;
            $cart_item->discount_amount -= $cart_item_data->discount_amount;
            $cart_item->save();

            session()->put('cart_total_items', $cart->number_of_items);


            return response()->json([
                'status_decrement' => 1,
                'message' => 'Item decreased...',
                'head' => 'Quantity decreased',
                'cart' => $cart,
                'cart_items' => $cart_item,
            ]);
        } else {
            return response()->json([
                'status_decrement' => 0,
                'message' => 'Cannot decrease quantity. Quantity already at minimum.',
                'head' => 'Unable to decrease quantity'
            ]);
        }
    }



    public function increment_item_checkout_page(Request $request)
    {
        $id = $request->input('id');

        $cart_item_data = Cart_Items::select('cart_items.*', 'products.*')
            ->leftJoin('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.id', $id)
            ->first();


        $Product_id = $cart_item_data->product_id;


        $res =  $this->check_quantity($Product_id, 1);

        if ($res == 2) {
            return response()->json(['out_of_stock' => 1]);
        }

        // this is logic to check incremnt product according quantity of  stocks

        // $Product_id = $cart_item_data->product_id;

        // $product = Products::find($Product_id);

        // $cart = Cart::where('user_id', Auth::user()->id)->first();

        // if ($cart != null) {

        //     $cart_items = Cart_Items::where('product_id', $Product_id)->where('cart_id', $cart->id)->first()->toArray();

        //     $cart_items_quantity = intval($cart_items['quantity']) + 1;

        //     if ($product->stock_quantity < $cart_items_quantity) {
        //         return response()->json(['out_of_stock' => 1]);
        //     }
        // }




        $user_id = Auth::user()->id;
        $cart = Cart::where('user_id', $user_id)->first();

        $cart->price += $cart_item_data->product_price;
        $cart->number_of_items += 1;
        $cart->sub_total += $cart_item_data->product_price - $cart_item_data->discount_amount;
        $cart->discount_amount += $cart_item_data->discount_amount;
        $cart->save();

        $cart_item = Cart_Items::find($id);
        $cart_item->quantity += 1;
        $cart_item->sub_total += $cart_item_data->product_price - $cart_item_data->discount_amount;
        $cart_item->total_price += $cart_item_data->product_price;
        $cart_item->discount_amount += $cart_item_data->discount_amount;
        $cart_item->save();

        session()->put('cart_total_items', $cart->number_of_items);

        return response()->json([
            'cart' => $cart,
            'cart_items' => $cart_item,
            'status_increment' => 1,
            'message' => 'Item Increased...',
            'head' => 'Quantity increased'
        ]);
    }


    public function deleted_Cart_Item(Request $request)
    {
        $id = $request->input('id');
        $cart_items = Cart_Items::find($id);

        $cart = Cart::where('user_id', Auth::user()->id)->first();
        $cart->price -= $cart_items->total_price;
        $cart->number_of_items -= $cart_items->quantity;
        $cart->discount_amount -= $cart_items->discount_amount;
        $cart->sub_total -= $cart_items->sub_total;
        $cart->save();


        session()->put('cart_total_items', $cart->number_of_items);


        $empty = 0;
        $cart_items->delete();

        if ($cart->number_of_items == 0) {
            $cart->delete();
            $empty  = 1;
        }

        return response()->json(['status_deleted' => 1, 'cart' => $cart, 'empty' => $empty]);
    }




    public function cancel_order_process(Request $request)
    {
        $id  = $request->input('id');

        $order_data = Order::find($id);
        $order_data->status = 2;
        $order_data->save();

        $order_items_data = Order_items::where('order_id', $id)->get();

        foreach ($order_items_data as $item) {

            $products =  Products::find($item->product_id);
            $products->stock_quantity += $item->quantity;
            $products->save();
        }


        return response()->json(['status_cancel_order' => 1]);
    }
}
