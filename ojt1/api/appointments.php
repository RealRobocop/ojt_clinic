<?php
/**
 * Appointments API Endpoints - Updated with Soft Delete
 * MediCare - Clinic Management System
 * Handles all CRUD operations for appointments with soft delete
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/db.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$action = isset($request_uri[0]) ? $request_uri[0] : '';
$id = isset($request_uri[1]) ? (int)$request_uri[1] : null;

function sendResponse($code, $message, $data = null) {
    http_response_code($code);
    echo json_encode([
        'success' => ($code >= 200 && $code < 300),
        'code' => $code,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    if ($request_method === 'GET') {
        if ($action === 'list') {
            // Get all active (non-deleted) appointments
            $stmt = $pdo->query("
                SELECT * FROM appointments 
                WHERE is_deleted = 0
                ORDER BY appointmentDate DESC, appointmentTime DESC
            ");
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'Appointments retrieved successfully', $appointments);
        }
        elseif ($action === 'deleted') {
            // Get all deleted appointments
            $stmt = $pdo->query("
                SELECT * FROM appointments 
                WHERE is_deleted = 1
                ORDER BY deleted_at DESC
            ");
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'Deleted appointments retrieved successfully', $appointments);
        }
        elseif ($action === 'all') {
            // Get all appointments including deleted
            $stmt = $pdo->query("
                SELECT * FROM appointments 
                ORDER BY appointmentDate DESC, appointmentTime DESC
            ");
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'All appointments retrieved successfully', $appointments);
        }
        elseif ($action === 'upcoming') {
            // Get upcoming appointments (only active)
            $stmt = $pdo->query("
                SELECT * FROM appointments 
                WHERE appointmentDate >= CURDATE() AND is_deleted = 0
                ORDER BY appointmentDate ASC, appointmentTime ASC
            ");
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'Upcoming appointments retrieved', $appointments);
        }
        elseif ($action === 'get' && $id) {
            // Get single appointment (only if not deleted)
            $stmt = $pdo->prepare("
                SELECT * FROM appointments 
                WHERE id = ? AND is_deleted = 0
            ");
            $stmt->execute([$id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($appointment) {
                sendResponse(200, 'Appointment retrieved successfully', $appointment);
            } else {
                sendResponse(404, 'Appointment not found');
            }
        }
        elseif ($action === 'patient' && $id) {
            // Get appointments for a specific patient (only active)
            $stmt = $pdo->prepare("
                SELECT * FROM appointments 
                WHERE patientId = ? AND is_deleted = 0
                ORDER BY appointmentDate DESC, appointmentTime DESC
            ");
            $stmt->execute([$id]);
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'Patient appointments retrieved', $appointments);
        }
        else {
            sendResponse(400, 'Invalid action');
        }
    }
    elseif ($request_method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['patientId', 'patientName', 'appointmentDate', 'appointmentTime', 'appointmentType', 'status'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendResponse(400, "Field '$field' is required");
            }
        }
        
        // Verify patient exists and is not deleted
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$data['patientId']]);
        if (!$stmt->fetch()) {
            sendResponse(404, 'Patient not found');
        }
        
        // Check for duplicate appointment time (only among active appointments)
        $stmt = $pdo->prepare("
            SELECT id FROM appointments 
            WHERE patientId = ? AND appointmentDate = ? AND appointmentTime = ? AND is_deleted = 0
        ");
        $stmt->execute([$data['patientId'], $data['appointmentDate'], $data['appointmentTime']]);
        if ($stmt->fetch()) {
            sendResponse(409, 'Patient already has an appointment at this time');
        }
        
        // Insert appointment
        $stmt = $pdo->prepare("
            INSERT INTO appointments (
                patientId, patientName, appointmentDate, appointmentTime, 
                appointmentType, status, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            (int)$data['patientId'],
            $data['patientName'],
            $data['appointmentDate'],
            $data['appointmentTime'],
            $data['appointmentType'],
            $data['status'],
            $data['notes'] ?? null
        ]);
        
        $appointment_id = $pdo->lastInsertId();

        // Also insert into Master_appointments for record keeping
        $masterStmt = $pdo->prepare("
            INSERT INTO Master_appointments (
                patientId, patientName, appointmentDate, appointmentTime, 
                appointmentType, status, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $masterStmt->execute([
            (int)$data['patientId'],
            $data['patientName'],
            $data['appointmentDate'],
            $data['appointmentTime'],
            $data['appointmentType'],
            $data['status'],
            $data['notes'] ?? null
        ]);
        
        // Fetch and return created appointment
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendResponse(201, 'Appointment created successfully', $appointment);
    }
    elseif ($request_method === 'PUT') {
        if (!$id) {
            sendResponse(400, 'Appointment ID required');
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if appointment exists and is not deleted
        $stmt = $pdo->prepare("SELECT id FROM appointments WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            sendResponse(404, 'Appointment not found');
        }
        
        // Update appointment
        $updates = [];
        $params = [];
        $allowed_fields = ['appointmentDate', 'appointmentTime', 'appointmentType', 'status', 'notes'];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            sendResponse(400, 'No fields to update');
        }
        
        $params[] = $id;
        $query = "UPDATE appointments SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        // Fetch and return updated appointment
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendResponse(200, 'Appointment updated successfully', $appointment);
    }
    elseif ($request_method === 'DELETE') {
        if (!$id) {
            sendResponse(400, 'Appointment ID required');
        }
        
        // Check if appointment exists and is not already deleted
        $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$appointment) {
            sendResponse(404, 'Appointment not found or already deleted');
        }
        
        // Soft delete: Mark as deleted with timestamp
        $stmt = $pdo->prepare("
            UPDATE appointments 
            SET is_deleted = 1, deleted_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        sendResponse(200, 'Appointment deleted successfully (soft delete)', $appointment);
    }
    // RESTORE request (Restore soft deleted appointment)
    elseif ($request_method === 'PATCH') {
        if (!$id) {
            sendResponse(400, 'Appointment ID required');
        }
        
        // Check if request is for restore
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['action']) && $data['action'] === 'restore') {
            // Check if appointment is deleted
            $stmt = $pdo->prepare("SELECT id FROM appointments WHERE id = ? AND is_deleted = 1");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendResponse(404, 'Appointment not found or not deleted');
            }
            
            // Restore appointment
            $stmt = $pdo->prepare("
                UPDATE appointments 
                SET is_deleted = 0, deleted_at = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Fetch and return restored appointment
            $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
            $stmt->execute([$id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            sendResponse(200, 'Appointment restored successfully', $appointment);
        }
    }
    else {
        sendResponse(405, 'Method not allowed');
    }
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    sendResponse(500, 'Database error occurred');
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    sendResponse(500, 'An error occurred');
}
?>
