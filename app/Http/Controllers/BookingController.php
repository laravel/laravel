<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\Room;
use Stripe\Checkout\Session;

class BookingController extends Controller
{
    //
    public function index()
    {
        $booking = Booking::all(); 
        $roomtypes = RoomType::all();
        return view('booking.index',compact('booking'));
    }

    public function create()
    {
        $roomtypes = RoomType::all();
        
        return view('booking.create',compact('roomtypes'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'guest_name'=>'required',
            'guest_email'=>'unique:bookings,guest_email',
            'check_in_date'=>'required',
            'check_out_date'=>'required',
            'room_type_id'=>'',
            'num_guests'=>'required',
            'total_price'=>'required',
            'booking_status'=>'required'

        ]);
        

        $booking = new Booking();
        

        $booking->guest_name = $request->guest_name;
        $booking->guest_email = $request->guest_email;
        $booking->check_in_date = $request->check_in_date;
        $booking->check_out_date = $request->check_out_date;
        $booking->room_type_id = $request->room_type_id;
        
        $booking->num_guests = $request->num_guests;
        $booking->total_price = $request->total_price;
        $booking->booking_status = $request->booking_status;

        $booking->save();

        return redirect('admin/booking')->with('success', 'Booking created successfully');
    }


    public function show($id)
    {
        $booking = Booking::find($id);
        return view('booking.show',compact('booking'));
    }
     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $booking_id
     * @return \Illuminate\Http\Response
     */

    public function edit($booking_id)
    {
        $booking = Booking::find($booking_id);
        $roomtypes = RoomType::all();
        return view('booking.edit',['booking'=>$booking,'roomtypes'=>$roomtypes],compact('booking'));
    }

    public function update(Request $request, $booking_id)
    {
        $request->validate([
            'guest_name'=>'required',
            'guest_email'=>'required',
            'check_in_date'=>'required',
            'check_out_date'=>'required',
            'room_type_id'=>'',
            'num_guests'=>'required',
            'total_price'=>'required',
            'booking_status'=>'required'

        ]);
        $booking = Booking::find($booking_id);
        $booking->guest_name = $request->guest_name;
        $booking->guest_email = $request->guest_email;
        $booking->check_in_date = $request->check_in_date;
        $booking->check_out_date = $request->check_out_date;
        $booking->room_type_id = $request->room_type_id;   
        $booking->num_guests = $request->num_guests;
        $booking->total_price = $request->total_price;
        $booking->booking_status = $request->booking_status;
        dd($booking_id);
        
        $booking->save();
  
        return redirect('admin/booking')->with('success', 'Booking updated successfully');
}


    public function destroy($id)
    {
        $booking = Booking::find($id);
        $booking->delete();

        return redirect('admin/booking')->with('success', 'Booking deleted successfully');
}
}