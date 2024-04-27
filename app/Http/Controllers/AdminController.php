<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Testimonial;
use Cookie;

class AdminController extends Controller
{
    
    function login(){
        return view('login');
       
    }
    // Check Login
    function check_login(Request $request){
        $request->validate([
            'username'=>'required',
            'password'=>'required',
        ]);
        $admin=Admin::where(['username'=>$request->username,'password'=>($request->password)])->count();
        if($admin>0){
            $adminData=Admin::where(['username'=>$request->username,'password'=>($request->password)])->get();
            session(['adminData'=>$adminData]);
             return redirect('dashboard')->with('msg','Login Successfully!!');
        }else{
            return redirect('admin/login')->with('msg','Invalid username/Password!!');
        }
    }
    // Logout
    function logout(){
        session()->forget(['adminData']);
        return redirect('admin');
    }

    function dashboard(){
        $bookings=Booking::selectRaw('count(id) as total_bookings,checkin_date')->groupBy('checkin_date')->get();
        $labels=[];
        $data=[];
        foreach($bookings as $booking){
            $labels[]=$booking['checkin_date'];
            $data[]=$booking['total_bookings'];
        }

        // For Pie Chart
        $rtbookings=DB::table('room_types as rt')
            ->join('rooms as r','r.room_type_id','=','rt.id')
            ->join('bookings as b','b.room_id','=','r.id')
            ->select('rt.*','r.*','b.*',DB::raw('count(b.id) as total_bookings'))
            ->groupBy('r.room_type_id')
            ->get();
        $plabels=[];
        $pdata=[];
        foreach($rtbookings as $rbooking){
            $plabels[]=$rbooking->detail;
            $pdata[]=$rbooking->total_bookings;
        }
        // End

        // echo '<pre>';
        // print_r($rtbookings);

        return view('dashboard',['labels'=>$labels,'data'=>$data,'plabels'=>$plabels,'pdata'=>$pdata]);
    }

    public function testimonials()
    {
        $data=Testimonial::all();
        return view('admin_testimonials',['data'=>$data]);
    }

    public function destroy_testimonial($id)
    {
       Testimonial::where('id',$id)->delete();
       return redirect('admin/testimonials')->with('success','Data has been deleted.');
    }

}
