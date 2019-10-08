<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\ResponseHelper;

class ProvinceController extends Controller
{
    /**
     * [GET] users/get
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
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
