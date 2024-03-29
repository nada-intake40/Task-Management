<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

Trait ApiResponse
{
    public static function ResponseSuccess(?array $data = null, ?string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return self::jsonResponse($data, $message, $statusCode);
    }

    private static function jsonResponse(?array $data, ?string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'body' => $data,
        ], $statusCode);
    }

    public static function ResponseFail(?array $data = null, ?string $message = 'Failed', int $statusCode = 400): JsonResponse
    {
        return self::jsonResponse($data, $message, $statusCode);
    }
}