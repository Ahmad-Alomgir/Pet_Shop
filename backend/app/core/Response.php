<?php

class Response
{
    /*
    |--------------------------------------------------------------------------
    | Send JSON Response
    |--------------------------------------------------------------------------
    */
    public static function json($data = [], $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode($data);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Send Success Response
    |--------------------------------------------------------------------------
    */
    public static function success($message = 'Success', $data = [])
    {
        self::json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Send Error Response
    |--------------------------------------------------------------------------
    */
    public static function error($message = 'Error', $status = 400)
    {
        self::json([
            'status' => 'error',
            'message' => $message
        ], $status);
    }
}