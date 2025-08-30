<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Muestra la vista del dashboard según el rol del usuario.
     */
    public function index()
    {
        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Verifica si el usuario existe y si tiene roles
        if ($user && $user->roles->first()) {
            $userRole = $user->roles->first()->name;
        } else {
            // Si el usuario no tiene un rol, asigna un valor por defecto o maneja el error
            $userRole = 'estudiante'; // O null, o redirige a una página de error
        }
        
        // Retorna la vista y pasa la variable del rol
        return view('dashboard', ['userRole' => $userRole]);
    }
}