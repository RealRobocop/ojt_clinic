<?php
/**
 * First City Providential College - Clinic Management System
 * Appointments AJAX Handler
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'getTodayAppointments':
            getTodayAppointments($pdo);
            break;
        
        case 'getTomorrowAppointments':
            getTomorrowAppointments($pdo);
            break;
        
        case 'getAppointments':
            getAppointments($pdo);
            break;
        
        case 'getAppointment':
            getAppointment($pdo);
            break;
        
        case 'addAppointment':
            addAppointment($pdo);
            break;
        
        case 'updateAppointment':
            updateAppointment($pdo);
            break;
        
        case 'checkIn':
            checkIn($pdo);
            break;
        
        case 'getNotifications':
            getNotifications($pdo);
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getTodayAppointments($pdo) {
    $status = $_POST['status'] ?? '';
    $sortBy = $_POST['sortBy'] ?? 'time-asc';
    
    $today = date('Y-m-d');
    
    // Get students appointments
    $sqlStudent = "SELECT a.*, 
                    CONCAT(s.first_name, ' ', s.last_name) as patient_name,
                    'Student' as patient_type
            FROM appointments a
            JOIN students s ON a.patient_id = s.Student_id AND a.patient_type = 'Student'
            WHERE a.appointment_date = :today AND a.is_deleted = 0 AND a.status != 'completed'";
    
    // Get employee appointments
    $sqlEmployee = "SELECT a.*, 
                    CONCAT(e.first_name, ' ', e.last_name) as patient_name,
                    'Employee' as patient_type
            FROM appointments a
            JOIN employees e ON a.patient_id = e.employee_id AND a.patient_type = 'Employee'
            WHERE a.appointment_date = :today AND a.is_deleted = 0 AND a.status != 'completed'";
    
    $params = [':today' => $today];
    
    if (!empty($status)) {
        $sqlStudent .= " AND a.status = :status";
        $sqlEmployee .= " AND a.status = :status";
        $params[':status'] = $status;
    }
    
    // Union both queries
    $sql = "($sqlStudent) UNION ($sqlEmployee)";
    
    // Add sorting
    switch ($sortBy) {
        case 'time-asc':
            $sql .= " ORDER BY appointment_time ASC";
            break;
        case 'time-desc':
            $sql .= " ORDER BY appointment_time DESC";
            break;
        case 'name-asc':
            $sql .= " ORDER BY patient_name ASC";
            break;
        case 'name-desc':
            $sql .= " ORDER BY patient_name DESC";
            break;
        default:
            $sql .= " ORDER BY appointment_time ASC";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();
    
    // Get stats
    $statsStmt = $pdo->prepare("
        SELECT 
            COUNT(*) as today,
            SUM(CASE WHEN status = 'checked-in' THEN 1 ELSE 0 END) as checkedIn
        FROM appointments
        WHERE appointment_date = :today AND is_deleted = 0 AND status != 'completed'
    ");
    $statsStmt->execute([':today' => $today]);
    $stats = $statsStmt->fetch();
    
    echo json_encode([
        'success' => true, 
        'appointments' => $appointments,
        'stats' => $stats
    ]);
}

function getTomorrowAppointments($pdo) {
    $sortBy = $_POST['sortBy'] ?? 'time-asc';
    
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    // Get students appointments
    $sqlStudent = "SELECT a.*, 
                    CONCAT(s.first_name, ' ', s.last_name) as patient_name
            FROM appointments a
            JOIN students s ON a.patient_id = s.Student_id AND a.patient_type = 'Student'
            WHERE a.appointment_date = :tomorrow AND a.is_deleted = 0";
    
    // Get employee appointments
    $sqlEmployee = "SELECT a.*, 
                    CONCAT(e.first_name, ' ', e.last_name) as patient_name
            FROM appointments a
            JOIN employees e ON a.patient_id = e.employee_id AND a.patient_type = 'Employee'
            WHERE a.appointment_date = :tomorrow AND a.is_deleted = 0";
    
    $params = [':tomorrow' => $tomorrow];
    
    // Union both queries
    $sql = "($sqlStudent) UNION ($sqlEmployee)";
    
    // Add sorting
    switch ($sortBy) {
        case 'time-asc':
            $sql .= " ORDER BY appointment_time ASC";
            break;
        case 'time-desc':
            $sql .= " ORDER BY appointment_time DESC";
            break;
        case 'name-asc':
            $sql .= " ORDER BY patient_name ASC";
            break;
        case 'name-desc':
            $sql .= " ORDER BY patient_name DESC";
            break;
        default:
            $sql .= " ORDER BY appointment_time ASC";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true, 
        'appointments' => $appointments
    ]);
}

function getAppointments($pdo) {
    $year = $_POST['year'] ?? '';
    $patientType = $_POST['patientType'] ?? '';
    $status = $_POST['status'] ?? '';
    
    $wheres = ["a.is_deleted = 0"];
    $params = [];
    
    if (!empty($year)) {
        $wheres[] = "YEAR(a.appointment_date) = :year";
        $params[':year'] = $year;
    }
    
    if (!empty($status)) {
        $wheres[] = "a.status = :status";
        $params[':status'] = $status;
    }
    
    $whereClause = implode(' AND ', $wheres);
    
    // Get students appointments
    $sqlStudent = "SELECT a.*, 
                    CONCAT(s.first_name, ' ', s.last_name) as patient_name,
                    'Student' as patient_type
            FROM appointments a
            JOIN students s ON a.patient_id = s.Student_id AND a.patient_type = 'Student'
            WHERE $whereClause";
    
    // Get employee appointments  
    $sqlEmployee = "SELECT a.*, 
                    CONCAT(e.first_name, ' ', e.last_name) as patient_name,
                    'Employee' as patient_type
            FROM appointments a
            JOIN employees e ON a.patient_id = e.employee_id AND a.patient_type = 'Employee'
            WHERE $whereClause";
    
    // Filter by patient type if specified
    if ($patientType === 'Student') {
        $sql = $sqlStudent;
    } elseif ($patientType === 'Employee') {
        $sql = $sqlEmployee;
    } else {
        $sql = "($sqlStudent) UNION ($sqlEmployee)";
    }
    
    $sql .= " ORDER BY appointment_date DESC, appointment_time DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'appointments' => $appointments]);
}

function getAppointment($pdo) {
    $id = $_POST['id'] ?? 0;
    
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = :id AND is_deleted = 0");
    $stmt->execute([':id' => $id]);
    $appointment = $stmt->fetch();
    
    if ($appointment) {
        echo json_encode(['success' => true, 'appointment' => $appointment]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
    }
}
function addAppointment($pdo) {
    $data = [
        ':patient_type' => $_POST['patientType'] ?? 'Student',
        ':patient_id' => $_POST['patientId'] ?? 0,
        ':appointment_date' => $_POST['appointmentDate'] ?? '',
        ':appointment_time' => $_POST['appointmentTime'] ?? '',
        ':appointment_type' => $_POST['appointmentType'] ?? '',
        ':status' => $_POST['status'] ?? 'pending',
        ':notes' => $_POST['notes'] ?? null
    ];

    if (!$data[':patient_id'] || !$data[':appointment_date'] || !$data[':appointment_time']) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    $sql = "INSERT INTO appointments (
        patient_type, patient_id, appointment_date, appointment_time, appointment_type, status, notes
    ) VALUES (
        :patient_type, :patient_id, :appointment_date, :appointment_time, :appointment_type, :status, :notes
    )";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($data);

    echo json_encode(['success' => $result]);
}
function updateAppointment($pdo) {
    $id = $_POST['appointmentId'] ?? 0;

    $data = [
        ':id' => $id,
        ':patient_type' => $_POST['patientType'] ?? 'Student',
        ':patient_id' => $_POST['patientId'] ?? 0,
        ':appointment_date' => $_POST['appointmentDate'] ?? '',
        ':appointment_time' => $_POST['appointmentTime'] ?? '',
        ':appointment_type' => $_POST['appointmentType'] ?? '',
        ':status' => $_POST['status'] ?? 'pending',
        ':notes' => $_POST['notes'] ?? null
    ];

    $sql = "UPDATE appointments SET
        patient_type = :patient_type,
        patient_id = :patient_id,
        appointment_date = :appointment_date,
        appointment_time = :appointment_time,
        appointment_type = :appointment_type,
        status = :status,
        notes = :notes
    WHERE id = :id AND is_deleted = 0";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($data);

    echo json_encode(['success' => $result]);
}

function checkIn($pdo) {
    $id = $_POST['id'] ?? 0;
    
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'checked-in' WHERE id = :id AND is_deleted = 0");
    $result = $stmt->execute([':id' => $id]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to check in appointment']);
    }
}

function getNotifications($pdo) {

    // Students
    $sqlStudent = "
        SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS patient_name
        FROM appointments a
        JOIN students s 
            ON a.patient_id = s.Student_id 
            AND a.patient_type = 'Student'
        WHERE 
            a.is_deleted = 0 
            AND a.status IN ('pending', 'confirmed')
    ";

    // Employees
    $sqlEmployee = "
        SELECT a.*, CONCAT(e.first_name, ' ', e.last_name) AS patient_name
        FROM appointments a
        JOIN employees e 
            ON a.patient_id = e.employee_id 
            AND a.patient_type = 'Employee'
        WHERE 
            a.is_deleted = 0 
            AND a.status IN ('pending', 'confirmed')
    ";

    // Combine results
    $sql = "($sqlStudent) UNION ALL ($sqlEmployee)
            ORDER BY appointment_date ASC, appointment_time ASC
            LIMIT 10";

    $stmt = $pdo->query($sql);
    $notifications = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
}
