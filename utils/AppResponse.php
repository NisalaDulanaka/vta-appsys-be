<?php

namespace Utils;

class AppResponse
{
    public static function success(array $data = [], int $statusCode = 200): array
    {
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        http_response_code($statusCode);

        return [
            'data' => $data,
            'statusCode' => $statusCode,
        ];
    }

    public static function error(array $errorData, int $statusCode = 500): array
    {
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        http_response_code($statusCode);

        return [
            'error' => $errorData,
            'statusCode' => $statusCode,
        ];
    }
}
