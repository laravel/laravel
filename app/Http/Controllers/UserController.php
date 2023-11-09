<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware(middleware: 'can:level')->only(methods: 'edit');
    }
     
    public function index() {
        $users = DB::table('users')
                  ->orderBy('name')
                  ->paginate(5);
    
        return view('users.index', [
            'users' => $users
        ]);
    }
    public function edit($id){
        return view('users.edit', [
            'user' => User::findOrFail($id)
        ]);
    }
    public function update(Request $id) {
        User::where('id', $id)->update(request()->all());
         return redirect()->route(route: 'user.index');
    }
}
