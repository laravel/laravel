<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Exceptions\DataDeletionException;



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
        
        $data = new Banner;
        $imgPath = $request->file('banner_src')->store('/app/public/img');
         $data->banner_src = $imgPath;
         $data->alt_text = $request->alt_text;
        //$data->publish_status = $request->publish_status;
        $data->save();
        //$data = $this->banner->create($request->all());
        return response()->json(['message' => 'Banner Data Added Successfully', 'data' => $data]);
    }

    public function updateBanner(Request $request){
        $validator = $request->validate([
            'banner_id' => 'required',
        ]);
        $data = $this->banner->find($request->banner_id);
        if($data==null) {
            //throw new DataDeletionException("Banner not found.");
            die("Banner not found.");
        }
         else{            
        $data->update($request->all());
        return response()->json(['message' => 'Banner Data Updated Successfully', 'data' => $data]);

        }
       

        // $data->update($request->all());
        // return response()->json(['message' => 'Banner Data Updated Successfully', 'data' => $data]);

        
    }

    public function deleteBanner(Request $request){
        $validator = $request->validate([
            'banner_id' => 'required'
        ]);
        $data = $this->banner->find($request->banner_id);
        if(!$data) {
            throw new DataDeletionException("Customer not found.");
        }
        
        $data->delete();
        return response()->json(['message' => 'Banner Data Deleted Successfully']);
    }
}
