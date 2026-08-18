<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class AuthController extends Controller { public function form(){return view('auth.login');} public function login(Request $r){$d=$r->validate(['email'=>'required|email','password'=>'required']);if(!Auth::attempt($d,$r->boolean('remember')))return back()->withErrors(['email'=>'Invalid credentials.'])->onlyInput('email');$r->session()->regenerate();return redirect()->intended('/dashboard');} public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect('/login');} }
