<?php

namespace App\Http\Controllers;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function __construct(){
    //     $this->middleware('auth');
    // }


    public function index()
    {
        //
        $role =Role::all();
        return view('admin.role.index')->with(compact('role'));

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
        return view('admin.role.create')->with(compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name'=>'required|max:255',
            'desc'=>'required|max:255',
        ],
        [
            'name.required'=>'Vui lòng nhập tên quyền',
            'desc.required'=>'Vui lòng mô tả quyền'
        ]    
        );
        $role=new Role();
        $role->name= $data['name'];
        $role->description =$data['desc'];
        $role->save();
        return redirect()->back()->with('status','Thêm quyền thành công');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $role =Role::find($id);
        return view('admin.role.edit')->with(compact('role'));
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
        $role  = Role::find($id);
        $role->name = $data['name'];
        $role->description = $data['desc'];
        $role->save();
        // // dd($data);
        // // mã hóa password trước khi đẩy lên DB

        // // Tạo mới role với các dữ liệu tương ứng với dữ liệu được gán trong $data
      
        return redirect('/role')->with('status','Sửa người dùng thành công');
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
        $role  = Role::find($id);
        $role->delete();
        return redirect('/role')->with('status','Xóa người dùng thành công');
    }
}
