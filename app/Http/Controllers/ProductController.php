<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Posts;
use App\Models\Cart;
use App\Models\Cart_Items;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

use App\Models\Order_items;
use App\Models\Companies;
use Illuminate\Support\Facades\Redirect;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{

    public function showProductPage()
    {


        $categories = Products::join('category', 'products.category_id', '=', 'category.id')
            ->distinct('products.category_id')
            ->select('category.*')
            ->get();

        // return response()->json(['categories'=>$categories]);


        return view('products', ['category' => $categories]);
    }

    public function getCategories()
    {
        $categories = Category::select('id', 'name')->get()->toArray();
        return response()->json(['categories' => $categories]);
    }

    public function get_category_products_by_id($id)
    {
        // Fetch products with pagination
        $products = Products::leftJoin('posts', 'products.id', '=', 'posts.product_id')
            ->select('products.*', 'posts.image')
            ->where('category_id', $id)
            ->paginate(10); // Adjust the number per page as needed

        return view('categories_products')->with(['products' => $products]);
    }



    public function get_Product_single(Request $request, $id)
    {

        $product = Products::select('products.*', 'posts.image', 'companies.name as company_name')
            ->leftJoin('posts', 'products.id', '=', 'posts.product_id')
            ->leftJoin('companies', 'products.company_id', '=', 'companies.id')
            ->where('products.id', $id)
            ->first();

        return view('product_view', compact('product'));
    }

    public function showProductViewPage()
    {
        return view('product_view');
    }



    public function get_products_ProductPage()
    {


        $products = Products::leftJoin('posts', 'products.id', '=', 'posts.product_id')
        ->select('products.*', 'posts.image')
        ->orderBy('products.category_id')
        ->get()
        ->groupBy('category_id')
        ->map(function ($items) {
            return $items->take(5);
        })
        ->toArray();
    



        return response()->json(['products' => $products]);
    }

    public function showProductForm()
    {

        return view('product_add');
    }

    public function get_company_id(Request $request)
    {
        $id = $request->input('id');

        $companies = Companies::select('name', 'id')->where('category_id', $id)->get();

        return response()->json(['companies' => $companies]);
    }


    
    public function addProduct(Request $request)
    {



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


        $rules = [
            'productName' => 'required',
            'company' => 'required',
            'category' => 'required',
            'image' => 'required',
            'image.*' => 'required|image|mimes:jpg,jpeg,png|max:1500',
            'color' => 'required',
            'weight' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'discount' => 'required',
            'description' => 'required'
        ];

        $messages = [
            'productName.required' => 'The product name is required.',
            'company.required' => 'The company name is required.',
            'category.required' => 'The category is required.',
            'image.required' => 'The image is required.',
            'image.*.required' => 'Each image is required.',
            'image.*.image' => 'Each file must be an image.',
            'image.*.mimes' => 'Only jpg,jpeg,png images are allowed.',
            'image.*.max' => 'The maximum image size is 1500KB.',
            'color.required' => 'The color is required.',
            'weight.required' => 'The weight is required.',
            'quantity.required' => 'The quantity is required.',
            'price.required' => 'The price is required.',
            'discount.required' => 'The discount is required.',
            'description.required' => 'The description is required.'
        ];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $product = new Products();
        $product->name = $request->input('productName');
        $product->company_id = $request->input('company');
        $product->category_id = $request->input('category');
        $product->price = $request->input('price');
        $product->color = $request->input('color');
        $product->weight = $request->input('weight');
        $product->stock_quantity = $request->input('quantity');
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

        if ($product->id) {
            foreach ($request->file('image') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('storage/product_images/'), $filename);
                $post = new Posts();
                $post->product_id = $product->id;
                $post->image = $filename;
                $post->save();
            }
        }


        return response()->json(['message' => "Saved Successfully", 'status' => 1], 200);
    }




    public function add_to_cart(Request $request)
    {


        $id = $request->input('id');
        $quantity = $request->input('quantity');

        $res = $this->check_quantity($id, $quantity);

        if ($res == 1) {
            return response()->json(['products_out_of_stock' => 1]);
        } elseif ($res == 2) {
            return response()->json(['out_of_stock' => 1]);
        } elseif ($res == 3) {
            return response()->json(['max_limit' => 1]);
        }




        $product = Products::find($id);



        $cart = Cart::where('user_id', Auth::user()->id)->first();

        if ($cart) {
            $cart->price += $product->price * $quantity;
            $cart->number_of_items += $quantity;
            $cart->sub_total += ($product->price - $product->discount_amount) * $quantity;
            $cart->discount_amount += $product->discount_amount * $quantity;
            $cart->order_date = DB::raw('now()');
            // $cart->updated_at = DB::raw('now()');
            $cart->save();
        } else {
            $cart = new Cart();
            $cart->user_id = Auth::user()->id;
            $cart->price  = $product->price * $quantity;
            $cart->number_of_items = $quantity;
            $cart->sub_total = ($product->price - $product->discount_amount) * $quantity;
            $cart->discount_amount = $product->discount_amount * $quantity;
            $cart->order_date = DB::raw('now()');
            $cart->save();
        }


        $cart_items = Cart_Items::where('cart_id', $cart->id)->where('product_id', $product->id)->first();



        if ($cart_items != null) {
            $cart_items->quantity += $quantity;
            $cart_items->total_price += $product->price * $quantity;
            $cart_items->discount_amount += $product->discount_amount * $quantity;
            $cart_items->sub_total +=  ($product->price - $product->discount_amount) * $quantity;
            $cart_items->save();
        } else {
            $cart_items = new Cart_Items();
            $cart_items->cart_id = $cart->id;
            $cart_items->product_price = $product->price;
            $cart_items->product_id = $product->id;
            $cart_items->sub_total =  ($product->price - $product->discount_amount) * $quantity;
            $cart_items->quantity = $quantity;
            $cart_items->total_price = $product->price * $quantity;
            $cart_items->discount_amount = $product->discount_amount * $quantity;
            $cart_items->save();
        }

        $cart_total_items = $cart->number_of_items;

        session()->put('cart_total_items', $cart_total_items);


        // return response()->json(['product'=>$product,'quantity'=>$quantity]);
        return response()->json(['message' => 'Item added succesfully', 'head' => 'Item added', 'status' => 1, 'total_items' => $cart_total_items]);
    }
}
