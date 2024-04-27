<?php

namespace  App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ApiServiceController extends Controller
{
    protected $service;

    public function __construct(Service $service){
        $this->service = $service;
}
public function showservice()
{
    $data = $this->service->latest()->get();
    return response()->json(['message' => 'service Data Fetched Successfully', 'data' => $data]);
}

public function saveBanner(Request $request){
    $request->validate([
        'title'=>'required',
        'small_desc'=>'required',
        'detail_desc'=>'required',
        'photo'=>'required',
    ]);
    $data = $this->service->create($request->all());
    return response()->json(['message' => 'service Data Added Successfully', 'data' => $data]);
}
public function updateService(Request $request){
    $validator = $request->validate([
        'id'=>'required',
        'title'=>'required',
        'small_desc'=>'required',
        'detail_desc'=>'required'

    ]);
    $data = $this->service->find($request->banner_id);
    $data->update($request->all());
    return response()->json(['message' => 'service Data Updated Successfully', 'data' => $data]);
}

public function deleteBanner(Request $request){
    $validator = $request->validate([
        'id' => 'required'
    ]);
    $data = $this->service->find($request->id);
    $data->delete();
    return response()->json(['message' => 'service Data Deleted Successfully']);
}
}
