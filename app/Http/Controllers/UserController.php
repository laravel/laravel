<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ResponseHelper;

class UserController extends Controller
{
    /**
     * [GET] users/get
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $users = User::all()->sortBy("id");
        if (!$users) {
            return response()->json(null, 404);
        }

        return ResponseHelper::success([
            "users" => array_values($users->toArray())
        ]);
    }
}
