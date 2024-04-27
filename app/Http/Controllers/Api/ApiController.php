<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;


class ApiController extends Controller
{
    protected $banner;

    public function __construct(Banner $banner){
        $this->banner = $banner;
    }

    public function showBanner()
    {
        $data = $this->banner->latest()->get();
        return response()->json(['message' => 'Banner Data Fetched Successfully', 'data' => $data]);
    }

    public function saveBanner(Request $request){
        $request->validate([
            'banner_src' => 'required|image',
            'alt_text' => 'required',
        ]);
        $data = $this->banner->create($request->all());
        return response()->json(['message' => 'Banner Data Added Successfully', 'data' => $data]);
    }

    public function updateBanner(Request $request){
        $validator = $request->validate([
            'banner_id' => 'required',
        ]);
        $data = $this->banner->find($request->banner_id);
        $data->update($request->all());
        return response()->json(['message' => 'Banner Data Updated Successfully', 'data' => $data]);
    }

    public function deleteBanner(Request $request){
        $validator = $request->validate([
            'banner_id' => 'required'
        ]);
        $data = $this->banner->find($request->banner_id);
        $data->delete();
        return response()->json(['message' => 'Banner Data Deleted Successfully']);
    }
}
