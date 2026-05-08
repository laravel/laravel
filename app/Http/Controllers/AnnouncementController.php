<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        $unreadNotifications = UserNotification::where('user_id', Auth::id())
            ->where('is_read', false)->count();
        return view('customer.announcements', compact('announcements', 'unreadNotifications'));
    }

    public function adminIndex()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        Announcement::create([
            'title'   => '-',       // placeholder, hindi na ginagamit sa form
            'content' => $request->content,
            'type'    => 'info',    // default type
        ]);

        return back()->with('success', 'Announcement posted!');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $announcement->update([
            'content' => $request->content,
            'type'    => 'info',
        ]);

        return back()->with('success', 'Announcement updated!');
    }

    public function toggleActive(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        return back()->with('success', 'Status updated!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted!');
    }
}