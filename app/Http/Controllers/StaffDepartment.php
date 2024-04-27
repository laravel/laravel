<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

use function Laravel\Prompts\alert;

class StaffDepartment extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=Department::all();
        return view('department.index',['data'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('department.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=new Department; 
        $data->title=$request->title;
        $data->detail=$request->detail;
        $data->save();

        return redirect('admin/department')->with('success','Data has been added.');
    }
// else(title==''||detail=='') {
   
//     return redirect('admin/department/create')->with('error',('Please enter all the fields'));
// }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=Department::find($id);
        return view('department.show',['data'=>$data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $data=Department::find($id);
        return view('department.edit',['data'=>$data]);
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
        $data=Department::find($id);
        $data->title=$request->title;
        $data->detail=$request->detail;
        $data->save();

        return redirect('admin/department/')->with ('success','Data has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       Department::where('id',$id)->delete();
       return redirect('admin/department')->with('success','Data has been deleted.');
    }
}
