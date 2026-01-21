<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Mostrar lista de todos los usuarios (Solo Administrador).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::all();

        return view('admin.users', compact('users'));
    }

    /**
     * Mostrar el perfil del usuario autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function perfil()
    {
        $user = Auth::user();

        return view('perfil', compact('user'));
    }
}
