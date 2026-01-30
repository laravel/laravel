<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Associado;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login using session-based authentication
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais informadas estão incorretas.'],
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Log the login
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Usuário realizou login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'message' => 'Login realizado com sucesso',
        ]);
    }

    /**
     * Handle logout using session
     */
    public function logout(Request $request)
    {
        // Log the logout
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'logout',
            'description' => 'Usuário realizou logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
            ],
        ]);
    }

    /**
     * Get dashboard stats
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'associados' => Associado::where('ativo', true)->count(),
            'posts_publicados' => Post::where('status', 'publicado')->count(),
            'posts_rascunho' => Post::where('status', 'rascunho')->count(),
        ];

        if ($user->role === 'admin') {
            $stats['total_usuarios'] = User::count();
            $stats['total_editores'] = User::where('role', 'editor')->count();
            $stats['login_recentes'] = ActivityLog::where('action', 'login')
                ->with('user:id,name')
                ->latest()
                ->take(5)
                ->get();
        }

        if ($user->role === 'editor') {
            $stats['meus_posts'] = Post::where('user_id', $user->id)->count();
            $stats['meus_publicados'] = Post::where('user_id', $user->id)
                ->where('status', 'publicado')
                ->count();
        }

        return response()->json($stats);
    }

    /**
     * Get user profile (for editors)
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email', 'created_at']),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_profile',
            'description' => 'Usuário atualizou seu perfil',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso',
            'user' => $user->only(['id', 'name', 'email']),
        ]);
    }
}
