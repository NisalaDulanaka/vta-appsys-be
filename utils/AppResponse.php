<?php

namespace App\Utils;

class AppResponse {
    public static function success(array $data = [], int $statusCode = 200): array
    {
        return [
            'data' => $data,
            'statusCode' => $statusCode,
        ];
    }

    public static function error(array $errorData, int $statusCode = 500): array
    {
        return [
            'error' => $errorData,
            'statusCode' => $statusCode,
        ];
    }
}
