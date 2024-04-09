<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Companies;
use App\Models\Category;
use App\Models\Products;
use Illuminate\Validation\Rules\Can;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{


    public function current_password_check(Request $request)
    {

        $cuurent_password = $request->input('cuurent_password');

        $user_id = Auth::user()->id;

        $user_password = User::where('id', $user_id)->pluck('password');

        $serializedString = Crypt::decryptString($user_password[0]);

        $decrtpted_password = ($serializedString);



        if ($decrtpted_password  === $cuurent_password) {
            return response()->json(['current_password_status' => 1]);
        } else {
            return response()->json(['current_password_status' => 0]);
        }
    }

    public function change_password(Request $request)
    {

        $cuurent_password = $request->input('password');

        $user = User::find(Auth::user()->id);
        $user->password = Crypt::encrypt($request->input('password'));
        $user->save();


        return response()->json(['change_passowrd_status' => 1]);
    }




    public function change_password_view()
    {

        $view = view('change_password');
        return $view->render();
    }

    public function Profile_page($id)
    {
        $user = User::where('id', $id)->first();
        $name = $user->full_name;
        return view('profile_page')->with(['name' => $name]);
    }

    public function user_info()
    {
        $id = Auth::user()->id;

        $user_data = User::find($id);

        $view = view('user_info', compact('user_data'));

        return $view->render();
    }


    public function search_products_view()
    {
        $products =  session()->get('search_products');

        $view = view('search_product', compact('products'));

        session()->forget('search_products');

        return $view->render();
    }


    public function search_products(Request $request)
    {
        $searchTerm = Str::lower($request->input('searchQuery'));
    
        // Find company IDs with a case-insensitive search on the company name
        $companyIds = Companies::whereRaw('LOWER(name) LIKE ?', ["%$searchTerm%"])
                               ->pluck('id');
    
        // Find category IDs with a case-insensitive search on the category name or description
        $categoryIds = Category::whereRaw('LOWER(name) LIKE ? OR LOWER(description) LIKE ?', ["%$searchTerm%", "%$searchTerm%"])
                               ->pluck('id');
    
        // Find products with a case-insensitive search on the product name or description,
        // or belonging to the found categories or companies
        $products = Products::where(function ($query) use ($searchTerm, $categoryIds, $companyIds) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%$searchTerm%"])
                      ->orWhereRaw('LOWER(description) LIKE ?', ["%$searchTerm%"])
                      ->orWhereIn('category_id', $categoryIds)
                      ->orWhereIn('company_id', $companyIds);
            })
            ->leftJoin('posts', 'products.id', 'posts.product_id')
            ->select('products.*', 'posts.image')
            ->get();
    
        session()->put('search_products', $products);
    
        return response()->json(['status_products_search_navbar' => 1]);
    }
    



    public function Add_address()
    {

        return view('add_address_page');
    }

    public function Add_address_process(Request $request)
    {

        $user_id = Auth::user()->id;

        $address = new Address();

        $address->user_id = $user_id;
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->country = $request->input('country');
        $address->full_address = $request->input('address');
        $address->pincode = $request->input('pincode');
        $address->save();

        return response()->json(['status' => 1]);
    }

    public function get_address()
    {
        $id = Auth::user()->id;
        $user_address = Address::where('user_id', $id)->get();

        return view('change_address')->with(['address' => $user_address]);
    }

    public function get_roles()
    {

        $data = Role::all();

        return response()->json(['roles' => $data]);
    }




    public function create_user_admin(Request $request)
    {


        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'pincode' => 'required|digits:6',
            'email' => 'required|email|unique:user,email',
            'phone' => 'required|digits:10|unique:user,phone',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'address' => 'required',
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $role_id = $request->input('user_type_select');

        $roledata = Role::find($role_id);

        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->full_name = $request->input('first_name') . ' ' . $request->input('last_name');
        $user->email = $request->input('email');
        $user->password =  Crypt::encrypt($request->input('password'));
        $user->role_id = $role_id;
        $user->user_type = $roledata->role;
        $user->phone = $request->input('phone');
        $user->save();


        $address = new Address();
        $address->user_id = $user->id;
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->country = $request->input('country');
        $address->pincode = $request->input('pincode');
        $address->full_address = $request->input('address');
        $address->save();


        return response()->json(['user_created_status' => 1]);
    }




    public function get_user_by_id(Request $request)
    {
        $id = $request->input('id');

        $data = User::find($id);

        $address = Address::where('user_id', $data->id)->first();

        return response()->json(['data' => $data, 'status' => 1]);
    }

    public function edit_user_process(Request $request)
    {

        $role_id = $request->input('user_type_select_edit');

        $roledata = Role::find($role_id);


        $id = $request->input('user_edit_id_datatable');

        $user = User::find($id);
        $user->first_name = $request->input('first_name_edit');
        $user->last_name = $request->input('last_name_edit');
        $user->full_name = $request->input('first_name_edit') . ' ' . $request->input('last_name_edit');
        $user->email = $request->input('email_edit');
        $user->phone = $request->input('phone_edit');
        $user->role_id = $role_id;
        $user->user_type = $roledata->role;

        if ($request->input('password_edit') === $request->input('password_confirmation_edit') && $request->input('password_confirmation_edit') != ""  && $request->input('password_edit') != "") {
            $user->password =  Crypt::encrypt($request->input('password_edit'));
        }

        $user->save();

        return response()->json(['user_edit_status' => 1]);
    }



    public function user_delete_by_id(Request $request)
    {

        $id = $request->input('id');

        User::find($id)->delete();
        Address::where('user_id', $id)->delete();

        return response()->json(['user_delete_status' => 1]);
    }


    public function about_us_page()
    {
        $view =  view('about_us');
        return $view->render();
    }


    public function enter_email_forget_password(){

        $view = view('enter_email_forgot_password');

        return $view->render();

    }
















}
