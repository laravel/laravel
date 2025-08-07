<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Support\EnvWriter;
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
            'app_url' => 'nullable|url',
            'db_host' => 'nullable|string',
            'db_database' => 'nullable|string',
            'db_username' => 'nullable|string',
            'db_password' => 'nullable|string',
            'openai' => 'nullable|string',
            'stripe_public' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        EnvWriter::set(array_filter([
            'APP_URL' => $data['app_url'] ?? null,
            'DB_HOST' => $data['db_host'] ?? null,
            'DB_DATABASE' => $data['db_database'] ?? null,
            'DB_USERNAME' => $data['db_username'] ?? null,
            'DB_PASSWORD' => $data['db_password'] ?? null,
            'OPENAI_API_KEY' => $data['openai'] ?? null,
            'STRIPE_PUBLIC' => $data['stripe_public'] ?? null,
            'STRIPE_SECRET' => $data['stripe_secret'] ?? null,
        ]));

        Artisan::call('config:clear');

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

        Artisan::call('config:cache');

        return redirect()->route('dashboard')->with('status', 'Installed');
    }
}