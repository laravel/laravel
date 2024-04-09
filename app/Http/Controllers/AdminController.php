<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Order;
use App\Models\Cart_Items;
use App\Models\Cart;
use App\Models\Order_items;
use App\Models\Category;
use App\Models\Companies;
use App\Models\Products;
use App\Models\User;
use App\Models\Posts;
use App\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Role_Rights;
use App\Models\Rights;




class AdminController extends Controller
{


    public function show_admin_login()
    {
        return view("admin.dashboard");
    }


    // Datatables

    public function show_address_datatable()
    {
        return view("admin.address_datatable");
    }
    public function get_data_address_datatable()
    {

        $data = Address::get();
        return response()->json(['data' => $data]);
    }

    public function show_order_datatable()
    {
        return view("admin.order_datatable");
    }

    public function get_data_order_datatable()
    {

        $data = Order::leftJoin('address', 'order.address_id', '=', 'address.id')->
        leftJoin('user', 'order.user_id', '=', 'user.id')
            ->select('order.*', 'address.full_address','user.full_name')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function show_cart_datatable()
    {
        return view("admin.cart_datatable");
    }

    public function get_data_cart_datatable()
    {

        $data = Cart::get();
        return response()->json(['data' => $data]);
    }


    public function show_cart_items_datatable()
    {
        return view("admin.cart_items_datatable");
    }

    public function get_data_cart_items_datatable()
    {

        $data = Cart_Items::get();
        return response()->json(['data' => $data]);
    }

    public function show_category_datatable()
    {


        $data = [];


        if (Auth::user()->user_type != 'super_admin') {
            $role_id = Auth::user()->role_id;

            $right_id = Rights::where('pageName', 'category')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();
        }

        if (Auth::user()->user_type == 'super_admin') {

            $role_id = 2;

            $right_id = Rights::where('pageName', 'category')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();

            $data['is_delete'] = 1;
            $data['is_update'] = 1;
            $data['is_created'] = 1;
            $data['is_view'] = 1;
        }



        return view("admin.category_datatable")->with(['data' => $data]);
    }


    public function get_data_category_datatable()
    {

        $data = Category::get();
        return response()->json(['data' => $data]);
    }

    public function show_companies_datatable()
    {

        $data = [];


        if (Auth::user()->user_type != 'super_admin') {
            $role_id = Auth::user()->role_id;

            $right_id = Rights::where('pageName', 'companies')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();
        }

        if (Auth::user()->user_type == 'super_admin') {

            $role_id = 2;

            $right_id = Rights::where('pageName', 'companies')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();

            $data['is_delete'] = 1;
            $data['is_update'] = 1;
            $data['is_created'] = 1;
            $data['is_view'] = 1;
        }


        return view("admin.companies_datatable")->with(['data' => $data]);
    }

    public function get_data_companies_datatable()
    {

        $data = Companies::get();
        return response()->json(['data' => $data]);
    }

    public function show_order_items_datatable()
    {
        return view("admin.order_items_datatable");
    }

    public function get_data_order_items_datatable()
    {

        $data = Order_items::get();
        return response()->json(['data' => $data]);
    }

    public function show_posts_datatable()
    {
        return view("admin.posts_datatable");
    }


    public function get_data_posts_datatable()
    {

        $data = Posts::get();
        return response()->json(['data' => $data]);
    }





    public function show_products_datatable()
    {

        $data = [];


        if (Auth::user()->user_type != 'super_admin') {
            $role_id = Auth::user()->role_id;

            $right_id = Rights::where('pageName', 'products')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();
        }

        if (Auth::user()->user_type == 'super_admin') {

            $role_id = 2;

            $right_id = Rights::where('pageName', 'products')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();

            $data['is_delete'] = 1;
            $data['is_update'] = 1;
            $data['is_created'] = 1;
            $data['is_view'] = 1;
        }


        return view("admin.products_datatable")->with(['data' => $data]);
    }

    public function get_data_products_datatable()
    {


        $data = Products::leftJoin('companies', 'products.company_id', '=', 'companies.id')
            ->leftJoin('category', 'products.category_id', '=', 'category.id')
            ->select('products.*', 'category.name as category_name', 'companies.name as company_name')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function show_role_datatable()
    {
        return view("admin.role_datatable");
    }


    public function get_data_role_datatable()
    {

        $data = Role::get();
        return response()->json(['data' => $data]);
    }

    public function show_user_datatable()
    {


        $data = [];


        if (Auth::user()->user_type != 'super_admin') {

            $role_id = Auth::user()->role_id;

            $right_id = Rights::where('pageName', 'user')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();
        }

        if (Auth::user()->user_type == 'super_admin') {

            $role_id = 2;

            $right_id = Rights::where('pageName', 'user')->first();

            $data = Role_Rights::where('role_id', $role_id)->where('right_id', $right_id->id)->first();

            $data['is_delete'] = 1;
            $data['is_update'] = 1;
            $data['is_created'] = 1;
            $data['is_view'] = 1;
        }



        return view("admin.user_datatable")->with(['data' => $data]);
    }


    public function get_data_user_datatable()
    {

        $data = User::get();
        return response()->json(['data' => $data]);
    }


    // pproduct crud

    public function Delete_Product_Record(Request $request)
    {

        $id = $request->input('id');

        $record = Products::find($id);

        $status = 0;

        if ($record) {

            $post_data = Posts::where('product_id', $record->id)->first();
            $image = $post_data->image;

            $old_image_path = public_path('storage/product_images/') . $image;

            if (File::exists($old_image_path)) {
                File::delete($old_image_path);
            }

            $record->delete();
            $post_data->delete();

            $status = 1;
        }

        return response()->json(['product_delete_status' => $status]);
    }

    public function get_product_data(Request $request)
    {
        try {

            $status = 0;

            $id  = $request->input('id');
            // $data = Products::find($id)->leftJoin('posts', 'products.id', '=', 'posts.product_id')
            //     ->select('products.*', 'posts.image as product_image')->first();

            $data = Products::leftJoin('posts', 'products.id', '=', 'posts.product_id')
                ->select('products.*', 'posts.image as product_image')
                ->find($id);

            if ($data) {
                $status = 1;
                return response()->json(['data' => $data, 'status' => $status]);
            }
            return response()->json(['status' => $status]);
        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }


    public function edit_product_details(Request $request)
    {
        try {



            function calculateDiscountAmount($originalPrice, $discount)
            {
                if (strpos($discount, '%') !== false) {
                    // Convert percentage to decimal
                    $discountPercentage = (float) str_replace('%', '', $discount);
                    // Calculate discount amount
                    $discountAmount = $originalPrice * ($discountPercentage / 100);
                } else {
                    // Use discount directly
                    $discountAmount = (float) $discount;
                }
                return $discountAmount;
            }

            function calculateDiscountPercentage($originalPrice, $discount)
            {
                if (strpos($discount, '%') !== false) {
                    // Return discount percentage as it is
                    return (float) str_replace('%', '', $discount);
                } else {
                    // Calculate discount percentage
                    $discountAmount = (float) $discount;
                    $discountPercentage = ($discountAmount / $originalPrice) * 100;
                    return $discountPercentage;
                }
            }



            $id = $request->input('product_id_edit');
            $product = Products::find($id);
            $product->name = $request->input('productName');
            $product->category_id = $request->input('category');
            $product->company_id = $request->input('company');
            $product->color = $request->input('color');
            $product->weight = $request->input('weight');
            $product->stock_quantity = $request->input('quantity');
            $product->price = $request->input('price');
            $product->description = $request->input('description');

            if (!Str::contains($request->input('discount'), '%')) {
                $discount_percent  = intval(calculateDiscountPercentage($request->input('price'), $request->input('discount')));
                $product->discount_percent = $discount_percent . "%";
                $product->discount_amount = $request->input('discount');
            } else {
                $discount_amount = (calculateDiscountAmount($request->input('price'), $request->input('discount')));
                $product->discount_amount = $discount_amount;
                $product->discount_percent  =  $request->input('discount');
            }

            $product->save();

            if ($request->file('image')) {
                if ($product->id) {
                    foreach ($request->file('image') as $image) {
                        $filename = time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('storage/product_images/'), $filename);

                        // Remove the old image
                        $post = Posts::where('product_id', $product->id)->first();
                        if ($post) {

                            $old_image_path = public_path('storage/product_images/') . $post->image;

                            if (File::exists($old_image_path)) {
                                File::delete($old_image_path);
                            }
                            $post->product_id = $product->id;
                            $post->image = $filename;
                            $post->save();
                        }
                    }
                }
            }

            return response()->json(['status' => 1]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
