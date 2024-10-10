<?php


namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses {

    protected function ok($message)
    {
        $this->success($message,200);
    }
    protected function success($message, $data = [], $responseCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'status' => $responseCode,
        ], $responseCode);
    }

    protected function error($errors = [], $statusCode = null): JsonResponse
    {
        return response()->json([
            'errors' => $errors
        ]);
    }

}
