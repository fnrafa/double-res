<?php
require_once '../config/variable.php';
require_once '../config/db.php';
require_once 'functions.php';

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

if ($uri === '/api/classes' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['userId'];
    echo json_encode(addOrUpdateClass($data, $userId));
} elseif ($uri === '/api/classes' && $method === 'GET') {
    echo json_encode(getClasses());
} elseif (preg_match('/^\/api\/classes\/(\d+)$/', $uri, $matches) && $method === 'GET') {
    echo json_encode(getClassById($matches[1]));
} elseif ($uri === '/api/reservations' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['userId'];
    $classId = $data['classId'];
    echo json_encode(reserveClass($userId, $classId));
} elseif (preg_match('/^\/api\/reservations\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
    $reservationId = $matches[1];
    echo json_encode(cancelReservation($reservationId));
} elseif (preg_match('/^\/api\/classes\/(\d+)\/reservations$/', $uri, $matches) && $method === 'GET') {
    echo json_encode(getClassReservations($matches[1]));
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
