<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Thêm thư viện để mã hóa password
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        $users =User::all();
        $role =DB::table('users')
        ->join('roles', 'roles.id', '=', 'users.role_id')->get();
        // dd($role->name);
        return view('admin.users.index')->with(compact('role','users'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $role=Role::all();
        return view('admin.users.create')->with(compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        // Kiểm tra xem dữ liệu từ client gửi lên bao gốm những gì
        // dd($request);

        // gán dữ liệu gửi lên vào biến data
        $data = $request->all();
        // // dd($data);
        // // mã hóa password trước khi đẩy lên DB
        $data['password'] = Hash::make($request->password);

        // // Tạo mới user với các dữ liệu tương ứng với dữ liệu được gán trong $data
        User::create($data);
        return redirect()->back()->with('status','Thêm người dùng thành công');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        // return ();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // //
        $role=Role::all();
        $users =User::find($id);
        return view('admin.users.edit')->with(compact('role','users'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $data = $request->all();
        $user  = User::find($id);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role_id = $data['role_id'];
        $user->save();
        // // dd($data);
        // // mã hóa password trước khi đẩy lên DB

        // // Tạo mới user với các dữ liệu tương ứng với dữ liệu được gán trong $data
      
        return redirect('/users')->with('status','Sửa người dùng thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user  = User::find($id);
        $user->delete();
        return redirect('/users')->with('status','Xóa người dùng thành công');

    }
}
