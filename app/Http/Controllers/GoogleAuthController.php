<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Str;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Buscar usuario por google_id primero
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // Si no existe por google_id, buscar por email
                $user = User::where('email', $googleUser->email)->first();

                if ($user) {
                    // Usuario existe pero no tiene google_id - actualizarlo
                    $user->update(['google_id' => $googleUser->id]);
                    /*
                    // Si el usuario existe pero tenía otro método de login
                    if (empty($user->google_id)) {
                        // Actualizar con google_id
                        $user->google_id = $googleUser->id;
                        $user->save();
                    }
                    */
                } else {
                    // Crear nuevo usuario
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'email_verified_at' => now(),
                        'password' => bcrypt(Str::random(16)), // Contraseña aleatoria
                        // Añade aquí otros campos requeridos por Beeze
                    ]);
                }
            }

            // Autenticar al usuario
            Auth::login($user);
            request()->session()->put('2fa_user_id', $user->id);

            if ($user->google2fa_enabled)
                return redirect()->route('2fa.verify');


            return redirect('/dashboard'); // Ajusta según tu ruta

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }

}
