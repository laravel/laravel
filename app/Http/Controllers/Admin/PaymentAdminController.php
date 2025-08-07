<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentAdminController extends Controller
{
    public function index(): View
    {
        $payments = Payment::latest()->paginate(30);
        return view('admin.payments.index', compact('payments'));
    }

    public function approve(Payment $payment): RedirectResponse
    {
        if ($payment->status === 'pending' && $payment->provider === 'manual') {
            $payment->update(['status' => 'paid']);
            $payment->user->increment('credits', $payment->package?->credits ?? 0);
        }
        return back()->with('status', 'Payment approved');
    }

    public function fail(Payment $payment): RedirectResponse
    {
        if ($payment->status === 'pending' && $payment->provider === 'manual') {
            $payment->update(['status' => 'failed']);
        }
        return back()->with('status', 'Payment marked failed');
    }
}
