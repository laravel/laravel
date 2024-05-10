<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class ApiBookingController extends Controller
{
    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function showBooking(){
        $data = $this->booking->latest()->get();
        return response()->json(['message' => 'booking Data Fetched Successfully', 'data' => $data]);
    }

    public function saveBooking(Request $request){
        $request->validate([
            // 'guest_name'=>'required',
            // 'guest_email'=>'required',
            // 'check_in_date'=>'required',
            // 'check_out_date'=>'required',
            // 'room_id'=>'required',
            // 'num_guests'=>'required',
            // ' total_price'=>'required',
            // 'booking_status'=>'required',
        ]);
        $data =new Booking;
        $data->guest_name=$request->guest_name;
        $data->guest_email=$request->guest_email;
        $data->check_in_date=$request->check_in_date;
        $data->check_out_date=$request->check_out_date;
        $data->room_type=$request->room_type;
        $data->num_guests=$request->num_guests;
        $data->total_price=$request->total_price;
        $data->booking_status=$request->booking_status;
        

        $data = $this->booking->create($request->all());
        
        return response()->json(['message' => 'booking Data Added Successfully', 'data' => $data]);
    }

    public function updateBooking(Request $request){
        $validator = $request->validate([
            'booking_id' => 'required',
        ]);
        $data = $this->booking->find($request->booking_id);
        $data->update($request->all());
        return response()->json(['message' => 'booking Data Updated Successfully', 'data' => $data]);
}

    public function deleteBooking(Request $request){
        $validator = $request->validate([
            'booking_id' => 'required'
        ]);

        $data = $this->booking->find($request->booking_id);
        if($data==null) {
            return response()->json(['message' => 'booking not found.'], 404);
        }
         else{
            $data->delete();
            return response()->json(['message' => 'booking Data Deleted Successfully']);
    }
}
}