<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponseTrait
{
    protected function successResponse($data = [], $message = '', $statusCode = Response::HTTP_OK)
    {
        $response = [
            'code' => 1,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }
    protected function errorResponse($error, $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            'code' => 0,
            'error' => $error,
        ], $statusCode);
    }
}
