<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by search
        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        // Filter by action
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        // Filter by user
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter by date range
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30);

        $actions = ActivityLog::distinct()->pluck('action');
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.logs.index', compact('logs', 'actions', 'users'));
    }

    /**
     * Display the specified log details.
     */
    public function show(ActivityLog $log)
    {
        return view('admin.logs.show', compact('log'));
    }
}
