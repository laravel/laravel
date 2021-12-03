<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use app\Models\User;

class Controller extends BaseController
{
    public function getAllUsers() {
        $users = User::get()->toJson();
        return response($users, 200);
    }
    })
}
