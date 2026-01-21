<?php

namespace Modules\Web3\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WalletsController extends Controller
{

    public function isregistered($address)
    {
        $user = User::where('name', '=', $address)->first();
        if ($user) {
            // Inicia sesión al usuario
            Auth::login($user);
            return 1;
        }

        return 0;
    }

    public function register(Request $request)
    {
        if ($this->isregistered($request['address']) == 1)
            return 2;

        // Crea un usuario ficticio en la base de datos
        $user = User::create([
            'name' => $request['address'],
            'email' => $request['address'] . '@wallet.local',
            'password' => bcrypt(Carbon::now()),
            'timezone' => $request['timezone'],
            'email_verified_at' => Carbon::now(),
            //'role_id' => 2
        ]);

        return $this->isregistered($request['address']);
    }
}
