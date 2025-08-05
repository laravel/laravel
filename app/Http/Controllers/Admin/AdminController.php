<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AdminController extends Controller
{

    public function index()
    {
        return view('admin.dashboard');
    }
    
    public function create()
    {
        return view('admin.login');
    }


    public function store(Request $request)
    {
        $data = $request->all();
        if (Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return redirect('admin/dashboard');
        } else {
            return redirect()->back()->with('error_message', 'Invalid Email or Password');
        }
    }

    public function show(Admin $admin)
    {
        //
    }


    public function edit(Admin $admin)
    {
        //
    }


    public function update(Request $request, Admin $admin)
    {
        //
    }


    public function destroy(Admin $admin)
    {
        //
    }
}
