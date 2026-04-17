<?php

class Controller
{
    /*
    |--------------------------------------------------------------------------
    | Load Model
    |--------------------------------------------------------------------------
    */
    public function model($model)
    {
        if (class_exists($model)) {
            return new $model();
        }

        throw new Exception("Model $model not found");
    }

    /*
    |--------------------------------------------------------------------------
    | JSON Response
    |--------------------------------------------------------------------------
    */
    public function json($data = [], $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode($data);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Input Data (JSON / POST)
    |--------------------------------------------------------------------------
    */
    public function input()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            $data = $_POST;
        }

        return $data;
    }
}