<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
    public function success()
    {
        return $this->successResponse([
            'id' => 1,
            'name' => 'test',
        ], 'OK', 200, ['page' => 1]);
    }

    public function error()
    {
        return $this->errorResponse(
            'Bad Request',
            400,
            ['field' => ['invalid']],
            'VALIDATION_ERROR'
        );
    }
}
