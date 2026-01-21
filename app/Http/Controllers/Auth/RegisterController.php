<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Mostrar el formulario de registro.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Registrar un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:user,admin'
        ], [
            'name.required' => 'El usuario es requerido.',
            'nombre.required' => 'El nombre es requerido.',
            'apellidos.required' => 'Los apellidos son requeridos.',
            'email.required' => 'El email es requerido.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required' => 'Debes seleccionar un rol.',
            'role.in' => 'El rol no es válido.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}

