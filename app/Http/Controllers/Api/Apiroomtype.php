<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\RoomType;



class Apiroomtype extends Controller
{
    protected $RoomType;

    public function __construct(RoomType $RoomType){
        $this->RoomType = $RoomType;

    }
    public function showRoomtype()
    {
        $data = RoomType::all();
        return response()->json($data);
    }

   

    public function saveRoomtype(Request $request){
        $request->validate([
            'title' => 'required',
            'price' => 'required',
            'detail' => 'required',
            'img_src' => ' required',
    ]);

    $data=new Roomtype;
    $data->title=$request->title;
    $data->price=$request->price;
    $data->detail=$request->detail;
    $imgPath = $request->file('img_src')->store('/app/public/img');
    $data->img_src = $imgPath;
    $data->save();
    
   return response()->json(['message' => 'Roomtype Data Added Successfully', 'data' => $data]);
}


public function updateRoomtype(Request $request){
    $validator = $request->validate([
        'roomtype_id' => 'required',
    ]);
    $data = $this->RoomType->find($request->roomtype_id);
    if($data==null) {
        //throw new DataDeletionException("Banner not found.");
        die("Roomtype  not found.");
    }
     else{            
    $data->update($request->all());
    return response()->json(['message' => 'Roomtype Data Updated Successfully', 'data' => $data]);

    }

    // $data->update($request->all());
    // return response()->json(['message' => 'Roomtype Data Updated Successfully', 'data' => $data]);



}





public function deleteRoomtype(Request $request){
    $validator = $request->validate([
        'roomtype_id' => 'required'
    ]);
    $data = $this->RoomType->find($request->roomtype_id);
    if($data==null) {
       return response()->json(['message' => 'Roomtype Data Not Found']);
    }
    $data->delete();
    return response()->json(['message' => 'Roomtype Data Deleted Successfully']);
}
}