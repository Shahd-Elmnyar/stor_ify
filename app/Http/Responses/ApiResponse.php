<?php

namespace App\Http\Responses;

class ApiResponse
{
    /**
     * Success response.
     *
     * @param mixed $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [], $status = 200, $token = null)
    {

        $response = [
            'code' => 'SUCCESS',
            'data' => $data == [] ? (object)[] : $data, // Return empty object for data
        ];

        if ($token !== null) {
            $response['token'] = $token;
        }

        return response()->json($response, $status);
    }

    /**
     * Error response.
     *
     * @param string $message
     * @param int $status
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = 'ERROR', $status = 400, $errors = [])
    {
        return response()->json([
            'code' => $message,
            'data' => (object)[],
        ], $status);
    }
}
