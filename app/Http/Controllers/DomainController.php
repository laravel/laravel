<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DomainController extends Controller
{
    private array $reservedNames = ['admin', 'support', 'help', 'billing', 'www', 'mail', 'ftp', 'cpanel', 'webmail'];
    private array $allowedParentDomains = ['points.bd', 'clk.bd'];

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:63',
            'parent_domain' => 'required|string|in:points.bd,clk.bd'
        ]);

        $subdomain = strtolower($request->input('query'));
        $parentDomain = $request->input('parent_domain');
        $fullDomain = $subdomain . '.' . $parentDomain;

        if (in_array($subdomain, $this->reservedNames)) {
            return response()->json([
                'available' => false,
                'message' => 'This domain name is reserved.'
            ]);
        }

        // Check if length is less than 4 for premium
        $isPremium = strlen($subdomain) < 4;
        $price = $isPremium ? 500 : 0;

        $exists = Domain::where('domain_name', $fullDomain)->exists();

        return response()->json([
            'available' => !$exists,
            'domain' => $fullDomain,
            'subdomain' => $subdomain,
            'parent_domain' => $parentDomain,
            'is_premium' => $isPremium,
            'price' => $price
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'subdomain' => 'required|string|min:1|max:63',
            'parent_domain' => 'required|string|in:points.bd,clk.bd'
        ]);

        $subdomain = strtolower($request->input('subdomain'));
        $parentDomain = $request->input('parent_domain');
        $fullDomain = $subdomain . '.' . $parentDomain;

        if (in_array($subdomain, $this->reservedNames)) {
            throw ValidationException::withMessages(['subdomain' => 'This domain name is reserved.']);
        }

        if (Domain::where('domain_name', $fullDomain)->exists()) {
            throw ValidationException::withMessages(['subdomain' => 'This domain is already taken.']);
        }

        $isPremium = strlen($subdomain) < 4;
        $price = $isPremium ? 500 : 0;

        $user = $request->user();

        if ($price > 0 && $user->balance < $price) {
            return response()->json(['message' => 'Insufficient balance for premium domain. Please recharge.'], 402);
        }

        // Deduct balance if premium
        if ($price > 0) {
            $user->balance -= $price;
            $user->save();

            // Create billing transaction
            $user->billingTransactions()->create([
                'amount' => $price,
                'type' => 'payment',
                'description' => 'Registered premium domain: ' . $fullDomain,
                'status' => 'completed'
            ]);
        }

        // Create domain
        $domain = $user->domains()->create([
            'domain_name' => $fullDomain,
            'parent_domain' => $parentDomain,
            'status' => 'active',
            'expires_at' => now()->addYear()
        ]);

        // Trigger Cloudflare Mock integration here later...

        return response()->json([
            'message' => 'Domain registered successfully!',
            'domain' => $domain
        ], 201);
    }
}
