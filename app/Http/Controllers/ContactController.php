<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Store contact message (Public)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'message' => 'required',
            'name' => 'nullable',
            'reason' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contact = Contact::create($request->all());

        // Send Email Notification
        try {
            Mail::raw("New Contact Message:\n\nReason: {$contact->reason}\nEmail: {$contact->email}\nName: {$contact->name}\nMessage: {$contact->message}", function ($message) {
                $message->to('contact@homocerti.com')
                    ->subject('Homocerti - New Contact Request');
            });
        } catch (\Exception $e) {
            // Log error or continue
        }

        return response()->json(['message' => 'Contact saved', 'data' => $contact], 201);
    }

    /**
     * List contacts (Admin)
     */
    public function index()
    {
        return Contact::orderBy('created_at', 'desc')->get();
    }
}
