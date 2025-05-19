<?php

namespace App\Interfaces\Api\Controllers;

use App\Exceptions\CustomException;
use App\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function errorResponse(ValidationException | CustomException $e): JsonResponse
    {
        if($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], $e->getStatusCode());
        }
        else {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        }
    }

    protected function successResponse(string $message, array $data = [], int $code = 200): JsonResponse
    {
        if($data) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $code);
        } else {
            return response()->json([
                'success' => true,
                'message' => $message
            ], $code);
        }
    }
}
