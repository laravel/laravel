<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Post;

class DashboardController extends Controller
{
    /**
     * Show the editor dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_posts' => Post::where('user_id', $user->id)->count(),
            'published_posts' => Post::where('user_id', $user->id)->published()->count(),
            'draft_posts' => Post::where('user_id', $user->id)->where('status', 'rascunho')->count(),
            'total_views' => Post::where('user_id', $user->id)->sum('views'),
        ];

        $recentPosts = Post::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('editor.dashboard', compact('stats', 'recentPosts'));
    }
}
