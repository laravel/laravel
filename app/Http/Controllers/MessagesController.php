<?php

namespace App\Http\Controllers;

use App\Message;
use App\Rules\ValidPhone;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function store(Request $request)
    {

        $rules = [
            'name' => ['required', 'max:255'],
            'phone' => ['required', 'max:30', new ValidPhone],
            'message' => ['required', 'max:20000'], // Limit for TEXT is about 21844 in UTF8
        ];

        $data = $request->validate($rules);

        $message = new Message();
        $message->fill($data)->save();

        return response()->json($message);
    }
}