<?php

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Return JSON Response (Global)
|--------------------------------------------------------------------------
*/
function jsonResponse($data = [], $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');

    echo json_encode($data);
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Request Input (JSON / POST)
|--------------------------------------------------------------------------
*/
function getInput()
{
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        $data = $_POST;
    }

    return $data;
}

/*
|--------------------------------------------------------------------------
| Upload File
|--------------------------------------------------------------------------
*/
function uploadFile($file, $path)
{
    if (!isset($file) || $file['error'] !== 0) {
        return null;
    }

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    $filename = time() . '_' . basename($file['name']);
    $target = $path . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }

    return null;
}

/*
|--------------------------------------------------------------------------
| Generate URL for Uploaded File
|--------------------------------------------------------------------------
*/
function fileUrl($folder, $filename)
{
    if (!$filename) return null;

    return APP_URL . '/../uploads/' . $folder . '/' . $filename;
}

/*
|--------------------------------------------------------------------------
| Simple Password Hash
|--------------------------------------------------------------------------
*/
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

/*
|--------------------------------------------------------------------------
| Verify Password
|--------------------------------------------------------------------------
*/
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}