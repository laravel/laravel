<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return $this->successResponse([
            'name' => config('api.name'),
            'version' => config('api.version'),
        ], 'API is ready.');
    }
}
