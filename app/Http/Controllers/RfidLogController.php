<?php

namespace App\Http\Controllers;

use App\Models\RfidLog;
use Illuminate\Http\Request;

class RfidLogController extends Controller
{
    public function index()
    {
        return response()->json(RfidLog::with('student')->latest()->take(50)->get());
    }

    public function show(RfidLog $rfidLog)
    {
        return response()->json($rfidLog->load('student'));
    }
}
