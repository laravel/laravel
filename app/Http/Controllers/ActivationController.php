<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ActivationController extends Controller
{
    public function activate($token)
    {
        // Find the user by activation token
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            // Handle scenario where user is not found
            return redirect()->route('activation.error');
            // or return response()->json(['error' => 'Invalid activation token'], 404);
        }

        // Activate the user account
        $user->update([
            'activation_token' => null,
            'activated_at' => now(),
        ]);

        // Redirect to a success page or show a success message
        return redirect()->route('activation.success');
    }
    public function error()
{
    return view('activation.error');
}
}
