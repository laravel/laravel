<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:credit_packages,id']);
        $package = CreditPackage::findOrFail($request->input('package_id'));

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => URL::to('/dashboard').'?success=1',
            'cancel_url' => URL::to('/dashboard').'?canceled=1',
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($package->currency),
                    'product_data' => [
                        'name' => $package->name,
                    ],
                    'unit_amount' => (int) round($package->price * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'user_id' => $request->user()->id,
                'package_id' => $package->id,
            ],
        ]);

        Payment::create([
            'user_id' => $request->user()->id,
            'credit_package_id' => $package->id,
            'provider' => 'stripe',
            'provider_ref' => $session->id,
            'amount' => $package->price,
            'currency' => $package->currency,
            'status' => 'pending',
            'meta' => ['url' => $session->url],
        ]);

        return redirect($session->url);
    }

    public function webhook(Request $request)
    {
        $sig = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Exception $e) {
            return response('Invalid', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $payment = Payment::where('provider_ref', $session->id)->first();
            if ($payment && $payment->status !== 'paid') {
                $payment->update(['status' => 'paid']);
                $user = $payment->user;
                $package = $payment->package;
                $user->increment('credits', $package->credits);
            }
        }

        return response('OK');
    }
}
