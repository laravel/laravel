<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Operation completed successfully.', int $status = HttpResponse::HTTP_OK, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data ?? new \stdClass,
        ];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    public static function error(string $message = 'The request failed.', int $status = HttpResponse::HTTP_BAD_REQUEST, array $errors = [], ?string $code = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
            'errors' => $errors ?: new \stdClass,
        ];

        if ($code !== null) {
            $payload['code'] = $code;
        }

        return response()->json($payload, $status);
    }
}
