<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\login;
use App\Models\registro;
use Illuminate\Http\Request;

class loginController extends Controller
{
    
    public function login(Request $data)
    {
        $data = (object) $data;

        $username = $data->username;
        $password = $data->password;
    
        $querydata = registro::where('username', $username)->where('password', $password)->where('estado', 1)->get();

        if( $querydata != []  && count($querydata) > 0 ){
            $login = registro::where('username', $username)->get();

        }

        return response()->json($querydata);
    }
}
