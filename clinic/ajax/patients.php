<?php
/**
 * First City Providential College - Clinic Management System
 * Patients AJAX Handler - Separate Students & Employees Tables
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
        case 'getPatients':
            getPatients($pdo);
            break;
        
        case 'getPatient':
            getPatient($pdo);
            break;
        
        case 'addPatient':
            addPatient($pdo);
            break;
        
        case 'updatePatient':
            updatePatient($pdo);
            break;
        
        case 'deletePatient':
            deletePatient($pdo);
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getPatients($pdo) {
    $type = $_POST['type'] ?? '';
    $educationLevel = $_POST['educationLevel'] ?? '';
    $employeeType = $_POST['employeeType'] ?? '';
    $deptTrack = $_POST['deptTrack'] ?? '';
    $search = $_POST['search'] ?? '';
    
    $patients = [];

    // =========================
    // STUDENTS (TABLE: Students)
    // =========================
    if ($type === '' || $type === 'Student') {
        $sql = "SELECT 
                    'Student' AS patient_type,
                    Student_id AS id,
                    first_name,
                    last_name,
                    age,
                    gender,
                    mobile_no AS phone,
                    email,
                    education_lvl AS education_level,
                    year_lvl AS grade_level,
                    COALESCE(program, shs_strand) AS course_track,
                    NULL AS employee_type,
                    NULL AS department
                FROM Students
                WHERE is_deleted = 0";

        $params = [];

        if (!empty($educationLevel)) {
            $sql .= " AND education_lvl = :education_level";
            $params[':education_level'] = $educationLevel;
        }

        if (!empty($deptTrack)) {
            $sql .= " AND (program = :deptTrack OR shs_strand = :deptTrack)";
            $params[':deptTrack'] = $deptTrack;
        }

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY first_name ASC, last_name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = array_merge($patients, $stmt->fetchAll());
    }

    // =========================
    // EMPLOYEES (TABLE: Employees)
    // =========================
    if ($type === '' || $type === 'Employee') {
        $sql = "SELECT 
                    'Employee' AS patient_type,
                    employee_id AS id,
                    first_name,
                    last_name,
                    age,
                    gender,
                    mobile_no AS phone,
                    email,
                    employee_type,
                    department,
                    NULL AS education_level,
                    NULL AS grade_level,
                    NULL AS course_track
                FROM Employees
                WHERE is_deleted = 0";

        $params = [];

        if (!empty($employeeType)) {
            $sql .= " AND employee_type = :employee_type";
            $params[':employee_type'] = $employeeType;
        }

        if (!empty($deptTrack)) {
            $sql .= " AND department = :department";
            $params[':department'] = $deptTrack;
        }

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY first_name ASC, last_name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = array_merge($patients, $stmt->fetchAll());
    }

    // Sort combined results alphabetically
    usort($patients, function($a, $b) {
        return strcmp($a['first_name'], $b['first_name']);
    });

    echo json_encode(['success' => true, 'patients' => $patients]);
}

function getPatient($pdo) {
    $id = $_POST['id'] ?? 0;
    $type = $_POST['type'] ?? '';
    
    if ($type === 'Student') {
        $stmt = $pdo->prepare("SELECT 'Student' as patient_type, s.* FROM students s WHERE s.id = :id AND s.is_deleted = 0");
    } else {
        $stmt = $pdo->prepare("SELECT 'Employee' as patient_type, e.* FROM employees e WHERE e.id = :id AND e.is_deleted = 0");
    }
    
    $stmt->execute([':id' => $id]);
    $patient = $stmt->fetch();
    
    if ($patient) {
        echo json_encode(['success' => true, 'patient' => $patient]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
    }
}

function addPatient($pdo) {
    $patientType = $_POST['patientType'] ?? '';
    
    if (empty($patientType)) {
        echo json_encode(['success' => false, 'message' => 'Patient type is required']);
        return;
    }
    
    try {
        if ($patientType === 'Student') {
            $data = [
                ':first_name' => $_POST['firstName'] ?? '',
                ':last_name' => $_POST['lastName'] ?? '',
                ':age' => $_POST['age'] ?? null,
                ':gender' => $_POST['gender'] ?? '',
                ':phone' => $_POST['phone'] ?? null,
                ':email' => $_POST['email'] ?? null,
                ':address' => $_POST['address'] ?? null,
                ':student_id' => $_POST['studentId'] ?? null,
                ':education_level' => $_POST['educationLevel'] ?? '',
                ':grade_level' => $_POST['gradeLevel'] ?? '',
                ':course_track' => $_POST['courseTrack'] ?? ''
            ];
            
            // Validate required fields
            if (empty($data[':first_name']) || empty($data[':last_name']) || 
                empty($data[':education_level']) || empty($data[':grade_level']) || empty($data[':course_track'])) {
                echo json_encode(['success' => false, 'message' => 'All required student fields must be filled']);
                return;
            }
            
            $sql = "INSERT INTO students (
                first_name, last_name, age, gender, phone, email, address,
                student_id, education_level, grade_level, course_track
            ) VALUES (
                :first_name, :last_name, :age, :gender, :phone, :email, :address,
                :student_id, :education_level, :grade_level, :course_track
            )";
            
        } else { // Employee
            $data = [
                ':first_name' => $_POST['firstName'] ?? '',
                ':last_name' => $_POST['lastName'] ?? '',
                ':age' => $_POST['age'] ?? null,
                ':gender' => $_POST['gender'] ?? '',
                ':phone' => $_POST['phone'] ?? null,
                ':email' => $_POST['email'] ?? null,
                ':address' => $_POST['address'] ?? null,
                ':employee_id' => $_POST['employeeId'] ?? null,
                ':employee_type' => $_POST['employeeType'] ?? '',
                ':department' => $_POST['department'] ?? ''
            ];
            
            // Validate required fields
            if (empty($data[':first_name']) || empty($data[':last_name']) || 
                empty($data[':employee_type']) || empty($data[':department'])) {
                echo json_encode(['success' => false, 'message' => 'All required employee fields must be filled (first name, last name, employee type, department)']);
                return;
            }
            
            $sql = "INSERT INTO employees (
                first_name, last_name, age, gender, phone, email, address,
                employee_id, employee_type, department
            ) VALUES (
                :first_name, :last_name, :age, :gender, :phone, :email, :address,
                :employee_id, :employee_type, :department
            )";
        }
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($data);
        
        if ($result) {
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'type' => $patientType]);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updatePatient($pdo) {
    $id = $_POST['patientId'] ?? 0;
    $patientType = $_POST['patientType'] ?? '';
    
    if ($patientType === 'Student') {
        $data = [
            ':id' => $id,
            ':first_name' => $_POST['firstName'] ?? '',
            ':last_name' => $_POST['lastName'] ?? '',
            ':age' => $_POST['age'] ?? null,
            ':gender' => $_POST['gender'] ?? '',
            ':phone' => $_POST['phone'] ?? null,
            ':email' => $_POST['email'] ?? null,
            ':address' => $_POST['address'] ?? null,
            ':student_id' => $_POST['studentId'] ?? null,
            ':education_level' => $_POST['educationLevel'] ?? '',
            ':grade_level' => $_POST['gradeLevel'] ?? '',
            ':course_track' => $_POST['courseTrack'] ?? ''
        ];
        
        $sql = "UPDATE students SET
            first_name = :first_name,
            last_name = :last_name,
            age = :age,
            gender = :gender,
            phone = :phone,
            email = :email,
            address = :address,
            student_id = :student_id,
            education_level = :education_level,
            grade_level = :grade_level,
            course_track = :course_track
        WHERE id = :id AND is_deleted = 0";
        
    } else { // Employee
        $data = [
            ':id' => $id,
            ':first_name' => $_POST['firstName'] ?? '',
            ':last_name' => $_POST['lastName'] ?? '',
            ':age' => $_POST['age'] ?? null,
            ':gender' => $_POST['gender'] ?? '',
            ':phone' => $_POST['phone'] ?? null,
            ':email' => $_POST['email'] ?? null,
            ':address' => $_POST['address'] ?? null,
            ':employee_id' => $_POST['employeeId'] ?? null,
            ':employee_type' => $_POST['employeeType'] ?? '',
            ':department' => $_POST['department'] ?? ''
        ];
        
        $sql = "UPDATE employees SET
            first_name = :first_name,
            last_name = :last_name,
            age = :age,
            gender = :gender,
            phone = :phone,
            email = :email,
            address = :address,
            employee_id = :employee_id,
            employee_type = :employee_type,
            department = :department
        WHERE id = :id AND is_deleted = 0";
    }
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($data);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update patient']);
    }
}

function deletePatient($pdo) {
    $id = $_POST['id'] ?? 0;
    $type = $_POST['type'] ?? '';
    
    // Soft delete
    if ($type === 'Student') {
        $stmt = $pdo->prepare("UPDATE students SET is_deleted = 1 WHERE id = :id");
    } else {
        $stmt = $pdo->prepare("UPDATE employees SET is_deleted = 1 WHERE id = :id");
    }
    
    $result = $stmt->execute([':id' => $id]);
    
    if ($result) {
        // Also soft delete associated appointments
        $stmt = $pdo->prepare("UPDATE appointments SET is_deleted = 1 WHERE patient_type = :type AND patient_id = :id");
        $stmt->execute([':type' => $type, ':id' => $id]);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete patient']);
    }
}
?>
