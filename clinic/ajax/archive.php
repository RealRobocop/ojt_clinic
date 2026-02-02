<?php
/**
 * Manual Archive Script
 * Archives completed appointments to appointment_records table
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
        case 'archiveCompleted':
            archiveCompletedAppointments($pdo);
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function archiveCompletedAppointments($pdo) {
    // Get all completed appointments that aren't already archived
    $sql = "SELECT a.* FROM appointments a
            WHERE a.status = 'completed' 
            AND a.is_deleted = 0
            AND NOT EXISTS (
                SELECT 1 FROM appointment_records ar 
                WHERE ar.patient_id = a.patient_id 
                AND ar.appointment_date = a.appointment_date 
                AND ar.appointment_time = a.appointment_time
            )";
    
    $stmt = $pdo->query($sql);
    $appointments = $stmt->fetchAll();
    
    $archived = 0;
    $errors = [];
    
    foreach ($appointments as $apt) {
        try {
            if ($apt['patient_type'] === 'Student') {
                // Get student info
                $studentStmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
                $studentStmt->execute([':id' => $apt['patient_id']]);
                $student = $studentStmt->fetch();
                
                if ($student) {
                    $insertSql = "INSERT INTO appointment_records 
                        (patient_type, patient_id, patient_name, patient_gender, education_level, 
                         grade_level, course_track, employee_type, department, appointment_date, 
                         appointment_time, appointment_type, status, notes, record_year)
                        VALUES 
                        (:patient_type, :patient_id, :patient_name, :patient_gender, :education_level,
                         :grade_level, :course_track, NULL, NULL, :appointment_date,
                         :appointment_time, :appointment_type, :status, :notes, :record_year)";
                    
                    $insertStmt = $pdo->prepare($insertSql);
                    $insertStmt->execute([
                        ':patient_type' => 'Student',
                        ':patient_id' => $student['id'],
                        ':patient_name' => $student['first_name'] . ' ' . $student['last_name'],
                        ':patient_gender' => $student['gender'],
                        ':education_level' => $student['education_level'],
                        ':grade_level' => $student['grade_level'],
                        ':course_track' => $student['course_track'],
                        ':appointment_date' => $apt['appointment_date'],
                        ':appointment_time' => $apt['appointment_time'],
                        ':appointment_type' => $apt['appointment_type'],
                        ':status' => $apt['status'],
                        ':notes' => $apt['notes'],
                        ':record_year' => date('Y', strtotime($apt['appointment_date']))
                    ]);
                    $archived++;
                }
            } else {
                // Get employee info
                $employeeStmt = $pdo->prepare("SELECT * FROM employees WHERE id = :id");
                $employeeStmt->execute([':id' => $apt['patient_id']]);
                $employee = $employeeStmt->fetch();
                
                if ($employee) {
                    $insertSql = "INSERT INTO appointment_records 
                        (patient_type, patient_id, patient_name, patient_gender, education_level, 
                         grade_level, course_track, employee_type, department, appointment_date, 
                         appointment_time, appointment_type, status, notes, record_year)
                        VALUES 
                        (:patient_type, :patient_id, :patient_name, :patient_gender, NULL,
                         NULL, NULL, :employee_type, :department, :appointment_date,
                         :appointment_time, :appointment_type, :status, :notes, :record_year)";
                    
                    $insertStmt = $pdo->prepare($insertSql);
                    $insertStmt->execute([
                        ':patient_type' => 'Employee',
                        ':patient_id' => $employee['id'],
                        ':patient_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                        ':patient_gender' => $employee['gender'],
                        ':employee_type' => $employee['employee_type'],
                        ':department' => $employee['department'],
                        ':appointment_date' => $apt['appointment_date'],
                        ':appointment_time' => $apt['appointment_time'],
                        ':appointment_type' => $apt['appointment_type'],
                        ':status' => $apt['status'],
                        ':notes' => $apt['notes'],
                        ':record_year' => date('Y', strtotime($apt['appointment_date']))
                    ]);
                    $archived++;
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Appointment ID ' . $apt['id'] . ': ' . $e->getMessage();
        }
    }
    
    $response = [
        'success' => true,
        'archived' => $archived,
        'total' => count($appointments)
    ];
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    
    echo json_encode($response);
}
?>
