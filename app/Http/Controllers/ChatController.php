<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChatController extends Controller
{
    /**
     * Check if current time is within working hours (07:00 - 17:00)
     */
    private function isWorkingHours(): bool
    {
        $currentHour = Carbon::now()->hour;
        return $currentHour >= 7 && $currentHour < 17;
    }

    /**
     * Get chat list for current user
     */
    public function index()
    {
        if (!$this->isWorkingHours()) {
            return back()->with('warning', 'Chat hanya tersedia pada jam kerja (07:00 - 17:00)');
        }

        $user = auth()->user();
        
        // Get all teachers if user is siswa
        if ($user->role === 'siswa') {
            $contacts = User::where('role', 'guru')->get();
        } else {
            // Get siswa yang pernah chat
            $contacts = User::whereIn('id', function ($query) use ($user) {
                $query->select('sender_id')
                    ->from('chat_messages')
                    ->where('receiver_id', $user->id)
                    ->union(
                        User::select('receiver_id')
                            ->from('chat_messages')
                            ->where('sender_id', $user->id)
                    );
            })
            ->where('role', 'siswa')
            ->distinct()
            ->get();
        }

        return view('chat.index', ['chatUsers' => $contacts]);
    }

    /**
     * Show chat with specific user
     */
    public function show(User $user)
    {
        if (!$this->isWorkingHours()) {
            return back()->with('warning', 'Chat hanya tersedia pada jam kerja (07:00 - 17:00)');
        }

        $authUser = auth()->user();
        
        // Validate: siswa hanya bisa chat dengan guru, guru dengan siswa
        if ($authUser->role === 'siswa' && $user->role !== 'guru') {
            abort(403, 'Anda hanya dapat chat dengan guru');
        }
        if ($authUser->role === 'guru' && $user->role !== 'siswa') {
            abort(403, 'Anda hanya dapat chat dengan siswa');
        }

        // Get messages between two users
        $messages = ChatMessage::where(function ($query) use ($authUser, $user) {
            $query->where('sender_id', $authUser->id)
                ->where('receiver_id', $user->id);
        })
        ->orWhere(function ($query) use ($authUser, $user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $authUser->id);
        })
        ->orderBy('created_at')
        ->get();

        // Mark messages as read
        ChatMessage::where('sender_id', $user->id)
            ->where('receiver_id', $authUser->id)
            ->update(['is_read' => true]);

        return view('chat.show', compact('user', 'messages'));
    }

    /**
     * Send message
     */
    public function store(Request $request, User $user)
    {
        if (!$this->isWorkingHours()) {
            return back()->with('error', 'Chat hanya tersedia pada jam kerja (07:00 - 17:00)');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $authUser = auth()->user();

        // Validate roles
        if ($authUser->role === 'siswa' && $user->role !== 'guru') {
            abort(403, 'Anda hanya dapat chat dengan guru');
        }
        if ($authUser->role === 'guru' && $user->role !== 'siswa') {
            abort(403, 'Anda hanya dapat chat dengan siswa');
        }

        ChatMessage::create([
            'sender_id' => $authUser->id,
            'receiver_id' => $user->id,
            'message' => $validated['message'],
        ]);

        return redirect()->route('chat.show', $user);
    }

    /**
     * Get working hours status
     */
    public function workingHoursStatus()
    {
        $isWorking = $this->isWorkingHours();
        $currentHour = Carbon::now()->hour;
        
        return response()->json([
            'working' => $isWorking,
            'current_hour' => $currentHour,
            'message' => $isWorking ? 'Chat tersedia' : 'Chat hanya tersedia 07:00 - 17:00'
        ]);
    }
}
