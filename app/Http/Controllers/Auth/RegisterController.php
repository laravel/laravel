<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Crear el nuevo usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Obtener el rol de 'estudiante'
            $estudianteRole = \App\Models\Role::where('name', 'estudiante')->first();

            if ($estudianteRole) {
                // Asignar el rol al usuario
                $user->roles()->attach($estudianteRole->id);
            }

            // Iniciar sesión con el nuevo usuario
            auth()->login($user);

            // Redireccionar al usuario a una página de bienvenida
            return redirect('/dashboard');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }
}