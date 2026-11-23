<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;

class ApiError
{
    /**
     * Build the error envelope every API error shares (API design §5.2).
     *
     * @param  array<string, mixed>  $details
     * @param  array<string, mixed>  $headers
     */
    public static function response(int $status, string $code, string $message, array $details = [], array $headers = []): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => (object) $details,
            ],
            'meta' => [
                'request_id' => Context::get('request_id'),
            ],
        ], $status, $headers);
    }
}
