<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Role_Rights;
use App\Models\Rights;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Companies;
use App\Models\Products;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Mail;
use App\Mail\Forgot_password;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{


    public function reset_password_process(Request $request)
    {


        $email_token = $request->input('email_token');
        $password = $request->input('password');

        $user = User::where('email_token', $email_token)->first();
        $user->password = Crypt::encryptString($password);
        $user->save();


        return response()->json(['password_status' => 1]);
    }


    public function reset_password_page(Request $request, $token)
    {



        $parts = explode("=", $token);
        $token_value = $parts[1];


        $view = view('reset_password', compact('token_value'));
        return $view->render();
    }


    public function check_forget_passowrd_email(Request $request)
    {


        $email = $request->input('email');

        $data = User::where('email', $email)->first();

        if ($data) {


            $token =  Str::random(10);


            Mail::to($data->email)->send(new Forgot_password($token));

            $data->email_token = $token;
            $data->save();

            return response()->json(['status_found_email' => 1]);
        } else {
            return response()->json(['status_found_email' => 0]);
        }
    }

    public function product_view($id)
    {



        $product = Products::where('products.id', $id)
            ->leftJoin('posts', 'products.id', '=', 'posts.product_id')
            ->leftJoin('companies', 'products.company_id', '=', 'companies.id')
            ->leftJoin('category', 'products.category_id', '=', 'category.id')
            ->select('products.*', 'posts.image', 'companies.name as company_name', 'category.name as category_name', 'category.description as category_description')
            ->get();


        // return response()->json(['data' => $data]);

        $product = $product[0];


        $view  =  view('admin.product_view', compact('product'));

        return $view->render();
    }

    public function get_user_names()
    {

        $data = User::select('id', 'full_name')->get();
        return response()->json(['data' => $data]);
    }

    public  function  get_data_role_rights(Request $request)
    {


        $id = $request->input('id');

        $data = Role_Rights::where('role_id', $id)->get();

        return response()->json(['Role_data_status' => 1, 'data' => $data]);
    }


    public function Roles_submit(Request $request)
    {


        $id = $request->input('id');


        $user_rights =  Role_Rights::where('role_id', $id)->where('right_id', 1)->first();

        $user_rights->is_view = $request->input('is_view_user');
        $user_rights->is_update = $request->input('is_update_user');
        $user_rights->is_created = $request->input('is_created_user');
        $user_rights->is_delete = $request->input('is_delete_user');
        $user_rights->save();


        $companies_rights =  Role_Rights::where('role_id', $id)->where('right_id', 2)->first();

        $companies_rights->is_view = $request->input('is_view_companies');
        $companies_rights->is_update = $request->input('is_update_companies');
        $companies_rights->is_created = $request->input('is_created_companies');
        $companies_rights->is_delete = $request->input('is_delete_companies');
        $companies_rights->save();

        $category = Role_Rights::where('role_id', $id)->where('right_id', 3)->first();

        $category->is_view = $request->input('is_view_category');
        $category->is_update = $request->input('is_update_category');
        $category->is_created = $request->input('is_created_category');
        $category->is_delete = $request->input('is_delete_category');
        $category->save();

        $products = Role_Rights::where('role_id', $id)->where('right_id', 4)->first();

        $products->is_view = $request->input('is_view_products');
        $products->is_update = $request->input('is_update_products');
        $products->is_created = $request->input('is_created_products');
        $products->is_delete = $request->input('is_delete_products');
        $products->save();



        return response()->json(['Roles_changed_status' => 1]);
    }


    public function get_view_roles_rights()
    {

        if (Auth::user()->user_type != 'super_admin') {

            $user_role_id = Auth::user()->role_id;

            $data = Role_Rights::where('role_id', $user_role_id)
                ->leftJoin('rights', 'roll_rights.right_id', '=', 'rights.id')
                ->select('roll_rights.is_view', 'rights.pageName as rights_name')
                ->get();
            return response()->json(['data' => $data, 'status' => 1]);
        }

        return response()->json(['status' => 0]);
    }



    public function new_role_create(Request $request)
    {


        $role = new Role();
        $role->role = $request->input('role_name');
        $role->save();

        if ($role) {

            for ($i = 1; $i < 5; $i++) {

                $Roll_Rights  = new Role_Rights();
                $Roll_Rights->role_id = $role->id;
                $Roll_Rights->right_id = $i;
                $Roll_Rights->is_view = 0;
                $Roll_Rights->is_update = 0;
                $Roll_Rights->is_delete = 1;
                $Roll_Rights->is_created = 1;
                $Roll_Rights->save();
            }
        }


        return response()->json(['role_created_status' => 1]);
    }


    public function order_counts_dashboard()
    {


        $total = count(Order::get());


        $pending = count(Order::where('status', 0)->get());

        $completed = count(Order::where('status', 1)->get());


        $cancel = count(Order::where('status', 2)->get());

        $user = count(User::where('user_type', "user")->get());

        $products = count(Products::get());

        $out_of_stock = count(Products::where('stock_quantity', 0)->get());

        $todayOrdersTotalPrice = intval(Order::where('status', 1)->whereDate('updated_at', now()->toDateString())->sum('price'));

        $total_companies = count(Companies::get());


        // bar chart query
        $monthlyTotals = DB::table('order')->where('status', 1)
            ->select(DB::raw('EXTRACT(MONTH FROM order_date) as month'), DB::raw('SUM(price) as total_price'))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM order_date)'))
            ->orderBy('month')
            ->get();

        $monthlyTotalsInJson = $monthlyTotals->map(function ($total) {
            $monthName = date('M', mktime(0, 0, 0, $total->month, 10));
            return [$monthName => $total->total_price];
        })->reduce(function ($carry, $item) {
            return array_merge($carry, $item);
        }, []);



        // monthly user chart

        $userCount = DB::table('user')
            ->select(DB::raw('EXTRACT(MONTH FROM created_at) as month'), DB::raw('COUNT(*) as user_count'))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy('month')
            ->get();

        $monthlyUserCount = $userCount->map(function ($count) {
            $monthName = date('M', mktime(0, 0, 0, $count->month, 10));
            return [$monthName => $count->user_count];
        })->reduce(function ($carry, $item) {
            return array_merge($carry, $item);
        }, []);

    


        return response()->json([
            'order_status' => 1,
            'total' => $total,
            'products' => $products,
            'out_of_stock' => $out_of_stock,
            'total_companies' => $total_companies,
            'completed' => $completed,
            'cancel' => $cancel,
            'pending' => $pending,
            'user' => $user,
            'today_orders_sales' => $todayOrdersTotalPrice,
            'monthlyTotalsInJson' => $monthlyTotalsInJson,
            'monthly_user' => $monthlyUserCount, // Directly include the monthlyUserCount array here
        ]);
        

        
    }
}
