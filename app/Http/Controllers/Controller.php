<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function successResponse(mixed $data = null, string $message = 'Operation completed successfully.', int $status = 200, array $meta = []): JsonResponse
    {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    protected function errorResponse(string $message = 'The request failed.', int $status = 400, array $errors = [], ?string $code = null): JsonResponse
    {
        return ApiResponse::error($message, $status, $errors, $code);
    }
}
