<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InstallerController extends Controller
{
    public function index(): View
    {
        $errors = [];
        if (!is_writable(storage_path())) $errors[] = 'storage is not writable';
        if (!is_writable(base_path('bootstrap/cache'))) $errors[] = 'bootstrap/cache is not writable';
        $hasEnv = file_exists(base_path('.env'));
        $hasKey = (bool) config('app.key');

        return view('install.index', compact('errors','hasEnv','hasKey'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (!config('app.key')) {
            Artisan::call('key:generate', ['--ansi' => true]);
        }

        Artisan::call('migrate', ['--force' => true]);

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'role' => 'admin',
                'credits' => 1000,
            ]
        );

        Setting::updateOrCreate(['key' => 'installed'], ['value' => now()->toDateTimeString()]);

        return redirect()->route('dashboard')->with('status', 'Installed');
    }
}