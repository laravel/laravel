<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Store a new booking (Public)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'service' => 'required',
            'name' => 'nullable',
            'message' => 'nullable',
            'date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('message');
        $data['details'] = $request->input('message');

        $booking = Booking::create($data);

        // Send Email Notification
        try {
            Mail::raw("New Booking Request:\n\nService: {$booking->service}\nDate: {$booking->date}\nEmail: {$booking->email}\nName: {$booking->name}\nMessage: {$booking->details}", function ($message) {
                $message->to('contact@homocerti.com')
                    ->subject('Homocerti - New Booking Request');
            });
        } catch (\Exception $e) {
            // Log error or continue
        }

        return response()->json(['message' => 'Booking successful', 'data' => $booking], 201);
    }

    /**
     * List bookings (Admin)
     */
    public function index()
    {
        return Booking::orderBy('created_at', 'desc')->get();
    }

    /**
     * Update booking status (Admin)
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update($request->only('status'));
        return response()->json(['message' => 'Status updated']);
    }
}
