<?php
include_once 'auth.php';
include_once 'user.php';
require_once '../config/variable.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/api/auth/register' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    $response = registerUser($data->username, $data->password);
    echo json_encode($response);
} elseif ($uri === '/api/auth/login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    $response = loginUser($data->username, $data->password);
    echo json_encode($response);
} elseif (preg_match('/^\/api\/user\/(\d+)$/', $uri, $matches) && $method === 'GET') {
    $id = $matches[1];
    $response = getUserById($id);
    echo json_encode($response);
} elseif (preg_match('/^\/api\/user\/(\d+)$/', $uri, $matches) && $method === 'PUT') {
    $id = $matches[1];
    $data = json_decode(file_get_contents('php://input'));
    $response = updateUser($id, $data);
    echo json_encode($response);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Not found'
    ]);
}

function sendResponse($statusCode, $message, $data = null): array
{
    http_response_code($statusCode);
    $response = [
        'status' => (string)$statusCode,
        'message' => $message
    ];
    if (!is_null($data)) {
        $response['data'] = $data;
    }

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    return $response;
}