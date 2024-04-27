<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\RoomType;
use App\Models\Booking;
use Stripe\Checkout\Session;


// use Stripe\Stripe;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookings=Booking::all();
        return view('booking.index',['data'=>$bookings]);
     
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers=Customer::all();
        return view('booking.create',['data'=>$customers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'=>'required',
            'room_id'=>'required',
            'checkin_date'=>'required',
            'checkout_date'=>'required',
            'total_adults'=>'required',
            'total_children'=>'required',
            'roomprice'=>'required',
            'roomtype'=>'required',
            'payment_method'=>'required',
            'customer_name'=>'required'
        ]);
        
        
        if($request->ref=='front'){
            $sessionData=[
                'customer_id'=>$request->customer_id,
                'room_id'=>$request->room_id,
                'checkin_date'=>$request->checkin_date,
                'checkout_date'=>$request->checkout_date,
                'total_adults'=>$request->total_adults,
                'total_children'=>$request->total_children,
                'roomprice'=>$request->roomprice,
                'ref'=>$request->ref,
                'payment_method'=>$request->payment_method,
                'customer_name'=>$request->customer_name
            ];
            // session($sessionData);
            // \Stripe\Stripe::setApiKey('sk_test_51JKcB7SFjUWoS3CIIaPlxPSREpJYoyPsn5KIhj2CBCM9z23dRUreOUwFq6eXmRYmgXNfxSozplocikiAFe3aX7sK008OH0sqy6');
            // $session = \Stripe\Checkout\Session::create([
            //     'payment_method_types' => ['card'],
            //     'line_items' => [[
            //       'price_data' => [
            //         'currency' => 'inr',
            //         'product_data' => [
            //           'name' => 'T-shirt',
            //         ],
            //         'unit_amount' => $request->roomprice*100,
            //       ],
            //       'quantity' => 1,
            //     ]],
            //     'mode' => 'payment',
            //     'success_url' => 'http://localhost/laravel-apps/hotelManage/booking/success?session_id={CHECKOUT_SESSION_ID}',
            //     'cancel_url' => 'http://localhost/laravel-apps/hotelManage/booking/fail',
            // ]);
            // return redirect($session->url);
        }else{
            $data=new Booking();
            $data->customer_id=$request->customer_id;
            $data->room_id=$request->room_id;
            $data->checkin_date=$request->checkin_date;
            $data->checkout_date=$request->checkout_date;
            $data->total_adults=$request->total_adults;
            $data->total_children=$request->total_children;
            $data->roomtype=$request->roomtype;
            $data->payment_method=$request->payment_method;
            $data->customer_name=$request->customer_name;
            if($request->ref=='front'){
                $data->ref='front';
            }else{
                $data->ref='admin';
            }
            $data->save();
            
            return redirect('page/booking')->with('success','Data has been added.');
        }
        
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
   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Booking::where('id',$id)->delete();
        return redirect('admin/booking')->with('success','Data has been deleted.');
    }


    // Check Avaiable rooms
    function available_rooms(Request $request,$checkin_date){
        $arooms=DB::SELECT("SELECT * FROM rooms WHERE id NOT IN (SELECT room_id FROM bookings WHERE '$checkin_date' BETWEEN checkin_date AND checkout_date)");

        $data=[];
        foreach($arooms as $room){
            $roomTypes=RoomType::find($room->room_type_id);
            $data[]=['room'=>$room,'roomtype'=>$roomTypes];
        }
    }

  

    function booking_payment_success(Request $request) {
    //    \Stripe\Stripe::setApiKey('sk_test_51JKcB7SFjUWoS3CIIaPlxPSREpJYoyPsn5KIhj2CBCM9z23dRUreOUwFq6eXmRYmgXNfxSozplocikiAFe3aX7sK008OH0sqy6');
    
    //    $session = Session::retrieve($request->get('session_id'));
        
        // if ($session->payment_status == 'paid') {
        //     $customer = Customer::retrieve($session->customer);
    
            // Assuming you have the necessary session data stored in the session
            $data = new Booking;
            $data->customer_id = session('customer_id');
            $data->room_id = session('room_id');
            $data->checkin_date = session('checkin_date');
            $data->checkout_date = session('checkout_date');
            $data->total_adults = session('total_adults');
            $data->total_children = session('total_children');
            $data->roomtype = session('roomtype');
            $data->payment_method = session('payment_method');
            $data->customer_name = session('customer_name');
            $data->ref = (session('ref') == 'front') ? 'front' : 'admin';
            $data->save();
    
            return view('booking.success');
        }
        

    function booking_payment_fail(Request $request){
        return view('booking.failure');
    }
}
// }   