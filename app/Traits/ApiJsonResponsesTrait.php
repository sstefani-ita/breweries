<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiJsonResponsesTrait
{
    public function success(string $message, array $data, int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function error(string $message, int $code = 500): JsonResponse
    {
        return response()->json([
            'message' => $message
        ], $code);
    }
}
