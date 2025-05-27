<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialiteController extends Controller
{
    public function index(): View
    {
        $providers = [];
        foreach ((array)config('services', []) as $provider => $config) {
            if (is_array($config) && array_key_exists('client_id', $config) && array_key_exists('client_secret', $config)) {
                $providers[] = $provider;
            }
        }

        return view('socialite', compact('providers'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->flush();
        $request->session()->regenerate();

        return redirect(route('socialite.index'));
    }

    public function redirect(string $provider): SymfonyRedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect(route('socialite.index'))->withErrors(['error' => 'Failed to authenticate']);
        }

        $User = User::updateOrCreate([
            'socialite_provider' => $provider,
            'socialite_id' => $socialiteUser->getId(),
        ], [
            'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
            'email' => $socialiteUser->getEmail(),
        ]);

        Auth::login($User);

        return redirect(route('socialite.index'));
    }

    protected function validateProvider(string $provider): void
    {
        $provider = (array) config('services.'.$provider, []);
        if (! array_key_exists('client_id', $provider) || ! array_key_exists('client_secret', $provider)) {
            abort(404);
        }
    }
}
