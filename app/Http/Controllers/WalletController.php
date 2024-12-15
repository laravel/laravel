<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $balance = auth()->user()->transactions()->sum('amount');
        $transactions = auth()->user()->transactions()
            ->latest()
            ->get();
            
        return view('wallet.index', compact('balance', 'transactions'));
    }

    public function topup(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|in:10,20,50,100'
        ]);

        try {
            DB::beginTransaction();

            auth()->user()->transactions()->create([
                'amount' => $validated['amount'],
                'type' => 'topup',
                'description' => 'Wallet top-up'
            ]);

            DB::commit();
            return back()->with('success', 'Wallet topped up successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
} 