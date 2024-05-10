<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class ApiStaffController extends Controller
{
    protected $staff;
    public function __construct(staff $staff){
        $this->staff = $staff;

    }
    public function showStaff()
    {
        $data = $this->staff->latest()->get();
        return response()->json(['message' => 'Staff Data Fetched Successfully', 'data' => $data]);
        
    }
  

 public function saveStaff(Request $request){
   $request->validate([
        'full_name'=>'required',
       'department_id'=>'required',
         'image_src'=>'required',
         'Bio'=>'',
        'salary_type'=>'required',
        'salary_amt'=>'required'
     ]);
    $data=new Staff;
    $data->full_name=$request->full_name;
     $data->department_id=$request->department_id;
     $imgPath = $request->file('image_src')->store('/app/public/img');
     $data->image_src = $imgPath;
    $data->Bio=$request->Bio;
    $data->salary_type=$request->salary_type; 
    $data->salary_amt=$request->salary_amt;
    $data = $this->staff->create($request->all());
   return response()->json(['message' => 'staff Data Added Successfully', 'data' => $data]);
}

public function updateStaff(Request $request){
    $validator = $request->validate([
        'staff_id' => 'required',
    ]);
    $data = $this->staff->find($request->staff_id);

    $data->update($request->all());
    return response()->json(['message' => 'staff Data Updated Successfully', 'data' => $data]);
}

public function deleteStaff(Request $request){
    $validator = $request->validate([
        'staff_id' => 'required'
    ]);
    $data = $this->staff->find($request->staff_id);
    if($data==null) {
       return response()->json(['message' => 'staff Data Not Found']);
    }
     else{            
    $data->delete();
    return response()->json(['message' => 'staff Data Deleted Successfully']);
    }   
    // $data->delete();
    // return response()->json(['message' => 'staff Data Deleted Successfully']);
}

}