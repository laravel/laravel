<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class ApiStaffController extends Controller
{
    protected $staff;
    public function __construct(staff $staff){
        $this->staff = $Staff;

    }
    public function showStaff()
    {
        $data = $this->Staff->latest()->get();
        return response()->json(['message' => 'Banner Data Fetched Successfully', 'data' => $data]);
        
    }
  
    public function saveStaff(Request $request)
    {

        $request->validate([

        $imgPath=$request->file('photo')->store('public/imgs'),

       'full_name'=>'required',
        'department_id'=>'required',
        'photo'=>'$imgPath',
        'bio'=>' ',
        'salary_type'=>'require',
        'salary_amt'=>'required',
        ]);
        
        $data = $this->Staff->create($request->all());
        return response()->json(['message' => 'Staff Data Added Successfully', 'data' => $data]);
}

public function updateStaff(Request $request){
    $validator = $request->validate([
        'staff_id' => 'required',
    ]);
    $data = $this->Staff->find($request->staff_id);
    $data->update($request->all());
    return response()->json(['message' => 'staff Data Updated Successfully', 'data' => $data]);
}

public function deleteStaff(Request $request){
    $validator = $request->validate([
        'Staff_id' => 'required'
    ]);
    $data = $this->Staff->find($request->staff_id);
    $data->delete();
    return response()->json(['message' => 'staff Data Deleted Successfully']);
}

}