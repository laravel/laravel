<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class ResponseHelper {
    public static function success($data = [])
    {
        $response = [
            'status' => 'ok',
            'code' => 200,
            'data' => $data
        ];
        return response()->json($response);
    }

    public static function error($message, $code = 500, $httpCode = null)
    {
        if (is_string($message)) {
            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => $message
            ];
        }
        else {
            $data = array_merge([
                'status' => 'error',
                'code' => $code
            ], $message);
        }

        return response()->json(
            $data,
            $httpCode
                ? $httpCode
                : (is_numeric($code) && $code > 0 && $code < 1000
                ? $code
                : 400)
        );
    }
}
