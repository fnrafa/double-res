<?php
include_once '../config/db.php';
require_once '../config/variable.php';
require_once 'index.php';

function getClasses(): array
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM classes");
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($classes)) {
            return sendResponse(200, 'Success, but no classes available', []);
        }

        return sendResponse(200, 'Success', $classes);
    } catch (PDOException $e) {
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}

function getClassById($id): array
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$id]);
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($class) {
            return sendResponse(200, 'Success', $class);
        } else {
            return sendResponse(404, 'Class not found');
        }
    } catch (PDOException $e) {
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}

function reserveClass($userId, $classId): array
{
    global $pdo;

    $userResponse = @file_get_contents(REST_API_URL . "user/$userId");
    if ($userResponse === FALSE) {
        return sendResponse(500, 'Failed to connect to user service');
    }
    $userData = json_decode($userResponse, true);

    if ($userData['status'] != '200' || $userData['data']['membership_status'] != 'active') {
        return sendResponse(403, 'User not active or not authorized to reserve classes');
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$classId]);
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$class) {
            return sendResponse(404, 'Class not found');
        }

        $currentTime = new DateTime();
        $endDateTime = new DateTime($class['end']);
        if ($currentTime > $endDateTime) {
            return sendResponse(400, 'Class has already ended');
        }

        if ($class['quantity'] >= $class['capacity']) {
            return sendResponse(400, 'Class is fully booked');
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE member_id = ? AND class_id = ? AND reservation_date <= NOW() AND class_id != ?");
        $stmt->execute([$userId, $classId, $classId]);
        $reservationsCount = $stmt->fetchColumn();

        if ($reservationsCount >= $userData['data']['max_reservations']) {
            return sendResponse(400, 'Maximum reservations reached');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO reservations (member_id, class_id, reservation_date) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $classId]);

        $stmt = $pdo->prepare("UPDATE classes SET quantity = quantity + 1 WHERE id = ?");
        $stmt->execute([$classId]);

        $pdo->commit();

        return sendResponse(200, 'Class reserved successfully');
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    } catch (Exception $e) {
        return sendResponse(500, $e->getMessage());
    }
}


function cancelReservation($reservationId): array
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$reservationId]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation) {
            return sendResponse(404, 'Reservation not found');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$reservationId]);

        $stmt = $pdo->prepare("UPDATE classes SET quantity = quantity - 1 WHERE id = ?");
        $stmt->execute([$reservation['class_id']]);

        $pdo->commit();

        return sendResponse(200, 'Reservation canceled successfully');
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}

function getUserReservations($userId): array
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE member_id = ?");
        $stmt->execute([$userId]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return sendResponse(200, 'Success', $reservations);
    } catch (PDOException $e) {
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}

function getClassReservations($classId): array
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE class_id = ?");
        $stmt->execute([$classId]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return sendResponse(200, 'Success', $reservations);
    } catch (PDOException $e) {
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}


function addOrUpdateClass($classData, $userId): array
{
    global $pdo;
    $adminResponse = @file_get_contents(REST_API_URL . "user/$userId");
    if ($adminResponse === FALSE) {
        return sendResponse(500, 'Failed to connect to user service');
    }
    $adminData = json_decode($adminResponse, true);

    if ($adminData['status'] != '200' || $adminData['data']['role'] !== 'admin') {
        return sendResponse(403, 'Only admin can add or update classes. Current role: ' . $adminData['data']['role']);
    }

    if (empty($classData['name']) || empty($classData['start']) || empty($classData['end']) || empty($classData['capacity']) || !isset($classData['member_only'])) {
        return sendResponse(400, 'Some required fields are missing or invalid.');
    }

    $startDate = date('Y-m-d H:i:s', strtotime($classData['start']));
    $endDate = date('Y-m-d H:i:s', strtotime($classData['end']));

    $capacity = (int)$classData['capacity'];
    $memberOnly = (int)$classData['member_only'];

    try {
        if (empty($classData['id'])) {

            $stmt = $pdo->prepare("INSERT INTO classes (name, start, end, capacity, member_only) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$classData['name'], $startDate, $endDate, $capacity, $memberOnly]);
            return sendResponse(201, 'Class added successfully');
        } else {
            $stmt = $pdo->prepare("UPDATE classes SET name = ?, start = ?, end = ?, capacity = ?, member_only = ? WHERE id = ?");
            $stmt->execute([$classData['name'], $startDate, $endDate, $capacity, $memberOnly, $classData['id']]);
            return sendResponse(200, 'Class updated successfully');
        }
    } catch (PDOException $e) {
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}