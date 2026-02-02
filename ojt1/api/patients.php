<?php
/**
 * Patients API Endpoints - Updated with Soft Delete
 * MediCare - Clinic Management System
 * Handles all CRUD operations for patients with soft delete
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/db.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$action = isset($request_uri[0]) ? $request_uri[0] : '';
$id = isset($request_uri[1]) ? (int)$request_uri[1] : null;

// Error handling
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
    // GET requests
    if ($request_method === 'GET') {
        if ($action === 'list') {
            // Get all active (non-deleted) patients
            $stmt = $pdo->query("
                SELECT * FROM patients 
                WHERE is_deleted = 0
                ORDER BY dateAdded DESC
            ");
            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'Patients retrieved successfully', $patients);
        }
        elseif ($action === 'deleted') {
            // Get all deleted patients
            $stmt = $pdo->query("
                SELECT * FROM patients 
                WHERE is_deleted = 1
                ORDER BY deleted_at DESC
            ");
            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'Deleted patients retrieved successfully', $patients);
        }
        elseif ($action === 'all') {
            // Get all patients including deleted
            $stmt = $pdo->query("
                SELECT * FROM patients 
                ORDER BY dateAdded DESC
            ");
            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'All patients retrieved successfully', $patients);
        }
        elseif ($action === 'get' && $id) {
            // Get single patient (only if not deleted)
            $stmt = $pdo->prepare("
                SELECT * FROM patients 
                WHERE id = ? AND is_deleted = 0
            ");
            $stmt->execute([$id]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($patient) {
                sendResponse(200, 'Patient retrieved successfully', $patient);
            } else {
                sendResponse(404, 'Patient not found');
            }
        }
        elseif ($action === 'search') {
            // Search active patients only
            $query = isset($_GET['q']) ? $_GET['q'] : '';
            $stmt = $pdo->prepare("
                SELECT * FROM patients 
                WHERE is_deleted = 0 AND (
                    name LIKE ? OR phone LIKE ? OR email LIKE ?
                )
                ORDER BY name
            ");
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(200, 'Search results', $results);
        }
        else {
            sendResponse(400, 'Invalid action');
        }
    }
    // POST request (Create)
    elseif ($request_method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['name', 'age', 'gender'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendResponse(400, "Field '$field' is required");
            }
        }
        
        // Insert patient
        $stmt = $pdo->prepare("
            INSERT INTO patients (name, age, gender, phone, email, address, dateAdded)
            VALUES (?, ?, ?, ?, ?, ?, CURDATE())
        ");

        $stmt->execute([
            $data['name'],
            (int)$data['age'],
            $data['gender'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null
        ]);
        
        $patient_id = $pdo->lastInsertId();

        // Also insert into Master_patients for record keeping
        $masterStmt = $pdo->prepare("
            INSERT INTO Master_patients (name, age, gender, phone, email, address, dateAdded)
            VALUES (?, ?, ?, ?, ?, ?, CURDATE())
        ");

        $masterStmt->execute([
            $data['name'],
            (int)$data['age'],
            $data['gender'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null
        ]);
        
        // Fetch and return created patient
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendResponse(201, 'Patient created successfully', $patient);
    }
    // PUT request (Update)
    elseif ($request_method === 'PUT') {
        if (!$id) {
            sendResponse(400, 'Patient ID required');
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if patient exists and is not deleted
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            sendResponse(404, 'Patient not found');
        }
        
        // Update patient
        $updates = [];
        $params = [];
        $allowed_fields = ['name', 'age', 'gender', 'phone', 'email', 'address'];
        
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
        $query = "UPDATE patients SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        // Fetch and return updated patient
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendResponse(200, 'Patient updated successfully', $patient);
    }
    // DELETE request (Soft Delete)
    elseif ($request_method === 'DELETE') {
        if (!$id) {
            sendResponse(400, 'Patient ID required');
        }
        
        // Check if patient exists and is not already deleted
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            sendResponse(404, 'Patient not found or already deleted');
        }
        
        // Soft delete: Mark as deleted with timestamp
        $stmt = $pdo->prepare("
            UPDATE patients 
            SET is_deleted = 1, deleted_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        // Also soft delete associated appointments
        $appStmt = $pdo->prepare("
            UPDATE appointments 
            SET is_deleted = 1, deleted_at = CURRENT_TIMESTAMP 
            WHERE patientId = ? AND is_deleted = 0
        ");
        $appStmt->execute([$id]);
        
        sendResponse(200, 'Patient deleted successfully (soft delete)', $patient);
    }
    // RESTORE request (Restore soft deleted patient)
    elseif ($request_method === 'PATCH') {
        if (!$id) {
            sendResponse(400, 'Patient ID required');
        }
        
        // Check if request is for restore
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['action']) && $data['action'] === 'restore') {
            // Check if patient is deleted
            $stmt = $pdo->prepare("SELECT id FROM patients WHERE id = ? AND is_deleted = 1");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendResponse(404, 'Patient not found or not deleted');
            }
            
            // Restore patient
            $stmt = $pdo->prepare("
                UPDATE patients 
                SET is_deleted = 0, deleted_at = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Fetch and return restored patient
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$id]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            
            sendResponse(200, 'Patient restored successfully', $patient);
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
