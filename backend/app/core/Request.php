<?php

class Request
{
    /*
    |--------------------------------------------------------------------------
    | Get Method
    |--------------------------------------------------------------------------
    */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Input (JSON / POST)
    |--------------------------------------------------------------------------
    */
    public static function all()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            $data = $_POST;
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Input
    |--------------------------------------------------------------------------
    */
    public static function input($key, $default = null)
    {
        $data = self::all();
        return $data[$key] ?? $default;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Query Params
    |--------------------------------------------------------------------------
    */
    public static function query($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /*
    |--------------------------------------------------------------------------
    | Get File
    |--------------------------------------------------------------------------
    */
    public static function file($key)
    {
        return $_FILES[$key] ?? null;
    }
}