<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Role_Rights;
use App\Models\Rights;
use App\Models\Products;
use App\Models\Category;
use Illuminate\Support\Facades\Crypt;


class LoginController extends Controller
{

    public function logout()
    {
        Auth::logout();
        session()->forget('status_navbar');
        session()->forget('navbar_full_name');

        if (session()->has('address_id')) {
            session()->forget('address_id');
            session()->forget('user_address');
        }

        if (session()->has('admin_profile_name') && session()->has('admin_user_type')) {

            session()->forget('admin_profile_name');
            session()->forget('admin_user_type');
        }


        return redirect()->route('showLoginForm');
    }





    public function showLoginForm()
    {
        // session()->flush();
        return view('login');
    }

    public function homepage()
    {

        $category = Products::join('category', 'products.category_id', '=', 'category.id')
            ->distinct('products.category_id')
            ->select('category.*')
            ->get();


        $view =  view('home', compact('category'));
        return $view->render();
    }


    public function showRegisterForm()
    {
        return view('register');
    }

    public function registerProcess(Request $request)
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


        //  dd(Crypt::encrypt($request->input('password')),$request->input('password'));


        $user = new User();

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->full_name = $request->input('first_name') . ' ' . $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = Crypt::encrypt($request->input('password'));
        $user->role_id = 1;
        $user->user_type = 'user';
        $user->phone = $request->input('phone');
        $user->save();

        $id = $user->id;

        $address = new Address();
        $address->user_id = $id;
        $address->full_address = $request->input('address');
        $address->city = $request->input('city');
        $address->country = $request->input('country');
        $address->state = $request->input('state');
        $address->pincode = $request->input('pincode');
        $address->save();

        session()->put('user_id', $user->id);

        Auth::login($user);

        $user = Auth::user();
        $full_name = $user->full_name;

        session()->put('status_navbar', 1);
        session()->put('navbar_full_name', $full_name);



        return response()->json(['message' => "Registered Successfully", 'status' => 1], 200);
    }


    public function loginProcess(Request $request)
    {


        $credentials = $request->validate([
            'email_or_phone' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email_or_phone'])
            ->orWhere('phone', $credentials['email_or_phone'])
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Register your account', 'head' => 'account not found', 'status' => 0]);
        }

        $serializedString = Crypt::decryptString($user->password);
        // dd($serializedString);

        if (Str::contains($serializedString, ':') && Str::contains($serializedString, 's')) {
            $decrtpted_password = unserialize($serializedString);
        } else {
            $decrtpted_password = ($serializedString);
        }

        // dd($decrtpted_password,$credentials['password']);

        if ($decrtpted_password !== $credentials['password']) {
            return response()->json(['head' => 'Invalid credentials', 'message' => 'Enter correct credentials', 'status' => 0]);
        }


        Auth::login($user);

        if ($user->user_type != 'user') {



            $role = Role::find($user->role_id);
            session()->put('admin_user_type', $role->role);

            $fullName = Auth::user()->full_name;

            // Split the full name by space
            $nameParts = explode(' ', $fullName);

            // Get the first letter of the first name and last name
            $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
            $lastInitial = strtoupper(substr($nameParts[1], 0, 1));

            // Concatenate the initials
            $initials = $firstInitial . $lastInitial;
            session()->put('admin_profile_name', $initials);


            return response()->json(['admin_status_login' => 1]);
        }

        $user = Auth::user();
        $full_name = $user->full_name;

        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart == null) {
            session()->put('cart_total_items', 0);
        } else {
            session()->put('cart_total_items', $cart->number_of_items);
        }




        session()->put('status_navbar', 1);
        session()->put('navbar_full_name', $full_name);




        return response()->json(['message' => 'Logged in successfully', 'status' => 1]);
    }



    public function login_check_or_not()
    {

        $user = Auth::user();
        $numbers_of_items = 0;

        if ($user) {
            $cart_data = Cart::where('user_id', Auth::user()->id)->first();


            if ($cart_data) {
                $numbers_of_items = $cart_data->number_of_items;
            }
        }


        return response()->json(['number_of_items_navbar' => $numbers_of_items]);
    }
}
