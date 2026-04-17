<?php

class AdminMiddleware
{
    public static function handle()
    {
        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
            exit;
        }
    }
}