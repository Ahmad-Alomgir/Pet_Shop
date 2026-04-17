<?php

class UserMiddleware
{
    public static function handle()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'User unauthorized'
            ]);
            exit;
        }
    }
}
