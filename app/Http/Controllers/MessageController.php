<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Tampilkan daftar chat (list konversasi)
    public function index()
    {
        $user = auth()->user();
        
        // Ambil user terakhir yang di-chat
        $conversations = Message::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver'])
        ->latest('created_at')
        ->get();

        // Group by user yang di-chat
        $chatUsers = collect();
        foreach ($conversations as $message) {
            $otherUser = $message->sender_id === $user->id ? $message->receiver : $message->sender;
            if (!$chatUsers->contains('id', $otherUser->id)) {
                $chatUsers->push($otherUser);
            }
        }

        return view('chat.index', ['chatUsers' => $chatUsers, 'user' => $user]);
    }

    // Tampilkan chat dengan user tertentu
    public function show(User $user)
    {
        $authUser = auth()->user();
        
        // Ambil semua pesan antara kedua user
        $messages = Message::where(function ($query) use ($authUser, $user) {
            $query->where('sender_id', $authUser->id)->where('receiver_id', $user->id)
                  ->orWhere('sender_id', $user->id)->where('receiver_id', $authUser->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages sebagai read
        Message::where('receiver_id', $authUser->id)
                ->where('sender_id', $user->id)
                ->update(['is_read' => true]);

        return view('chat.show', ['user' => $user, 'messages' => $messages, 'authUser' => $authUser]);
    }

    // Simpan pesan baru
    public function store(Request $request, User $user)
    {
        $authUser = auth()->user();

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => $authUser->id,
            'receiver_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return redirect()->route('messages.show', $user)->with('success', 'Pesan terkirim');
    }
}
