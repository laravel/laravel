<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\{Auth,Hash,Password};
class AuthController extends Controller {
 public function form(){return view('auth.login');}
 public function login(Request $r){$d=$r->validate(['email'=>'required|email','password'=>'required']);if(!Auth::attempt($d,$r->boolean('remember')))return back()->withErrors(['email'=>'Invalid credentials.'])->onlyInput('email');$r->session()->regenerate();return redirect()->intended('/dashboard');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect('/login');}
 public function forgotPasswordForm(){return view('auth.forgot-password');}
 public function sendResetLink(Request $r){$r->validate(['email'=>'required|email']);$status=Password::sendResetLink($r->only('email'));return $status===Password::RESET_LINK_SENT?back()->with('status',__($status)):back()->withErrors(['email'=>__($status)]);}
 public function resetPasswordForm(Request $r,string $token){return view('auth.reset-password',['token'=>$token,'email'=>$r->query('email')]);}
 public function resetPassword(Request $r){$d=$r->validate(['token'=>'required','email'=>'required|email','password'=>'required|min:12|confirmed']);$status=Password::reset($d,function($user,$password){$user->forceFill(['password'=>Hash::make($password)])->save();});return $status===Password::PASSWORD_RESET?redirect('/login')->with('status',__($status)):back()->withErrors(['email'=>[__($status)]]);}
}
