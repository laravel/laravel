<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Associado;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_editors' => User::where('role', 'editor')->count(),
            'total_associados' => Associado::count(),
            'active_associados' => Associado::active()->count(),
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::where('status', 'rascunho')->count(),
        ];

        $recentLogs = ActivityLog::with('user')
            ->recent(10)
            ->get();

        $recentPosts = Post::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'recentPosts'));
    }
}
