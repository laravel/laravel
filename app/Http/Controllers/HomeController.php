<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Banner;
use App\Models\Roomtypeimage;
use App\Models\Room;
use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{

    // Home Page
    function home(){
        $banners=Banner::where('publish_status','on')->get();
        $services=Service::all();
        $roomTypes=RoomType::all();
        $testimonials=Testimonial::all();
        return View('home',['roomTypes'=>$roomTypes,'services'=>$services,'testimonials'=>$testimonials,'banners'=>$banners]);
    }

    // Service Detail Page
    function service_detail(Request $request, $id){
        $service=Service::find($id);
        return View('servicedetail',['service'=>$service]);
    }
    function roomtypes(Request $request, $id){
        $roomtypes= RoomType::all();
        return view('roomtype.index');
    }
    function rooms(){
        return view('room.index');
    }

    // Add Testimonial
    function add_testimonial(){
        return view('add-testimonial');
    }

    // Save Testimonial
    function save_testimonial(Request $request){
        $customerId=session('data')[0]->id;
        $data=new Testimonial;
        $data->customer_id=$customerId;
        $data->testi_cont=$request->testi_cont;
        $data->save();

        return redirect('customer/add-testimonial')->with('success','Data has been added.');
    }
}
