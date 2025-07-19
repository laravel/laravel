<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\QRCode\Bacon;

class TwoFactorAuthController extends Controller
{
    public function showEnableForm()
    {
        $user = Auth::user();

        if (!$user->google2fa_secret) {
            $user->generate2faSecret();
        }

        // Configura el servicio QR code según lo que instalaste
        $google2fa = (new Google2FA())
            ->setQRCodeService(new Bacon()); // Para Bacon/BaconQrCode

        $qrCodeUrl = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('2fa.enable', ['qrCodeUrl' => $qrCodeUrl, 'secret' => $user->google2fa_secret]);
    }

    public function enable(Request $request)
    {
        /*
        $request->validate([
            'code' => 'required',
        ]);
        */

        $user = Auth::user();

        if ($user->verify2faCode($request->code)) {
            $user->google2fa_enabled = true;
            $user->save();

            $request->session()->put('2fa_user_id', $user->id);

            return redirect()->route('dashboard')->with('success', '2FA habilitado correctamente');
        }

        return back()->with("error", "El código {$request->code}  es inválido o ha expirado");
    }

    public function showDisableForm()
    {
        return view('2fa.disable');
    }

    public function disable(Request $request)
    {
        $user = Auth::user();

        // Verificar con código 2FA en lugar de contraseña
        $request->validate([
            'code' => 'required',
        ]);

        if ($user->verify2faCode($request->code)) {
            $user->google2fa_enabled = false;
            $user->google2fa_secret = null;
            $user->save();

            return redirect()->route('dashboard')->with('success', '2FA deshabilitado correctamente');
        }

        return back()->with('error', 'Código de verificación inválido');
    }

    public function showVerifyForm()
    {
        return view('2fa.verify');
    }

    public function verify(Request $request)
    {
        /*
        $request->validate([
            'code' => 'required',
        ]);
        */

        $remember = $request->session()->pull('2fa_remember');
        if ($remember)
            request()->session()->put('2fa_remember', $remember);

        $userId = $request->session()->pull('2fa_user_id');
        if ($userId)
            request()->session()->put('2fa_user_id', $userId);
        else {
            Auth::logout();
            return redirect()->route('home');
        }

        $user = User::findOrFail($userId);

        if ($user->verify2faCode($request->code)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();
            session(['2fa_verified' => true]);

            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'Código de verificación inválido');
    }
}