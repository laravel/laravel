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
                } else {
                    // Crear nuevo usuario
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => bcrypt(Str::random(16)), // Contraseña aleatoria
                        // Añade aquí otros campos requeridos por Beeze
                    ]);
                }
            }

            // Autenticar al usuario
            Auth::login($user);

            return redirect('/dashboard'); // Ajusta según tu ruta

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }

}
