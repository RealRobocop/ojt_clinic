<?php
/**
 * First City Providential College - Clinic Management System
 * Import Handler - Supports Excel, CSV, SQL
 */

require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$importType = $_POST['importType'] ?? '';
$imported = 0;

try {
    if (!isset($_FILES['importFile']) || $_FILES['importFile']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }
    
    $file = $_FILES['importFile'];
    $fileName = $file['name'];
    $tmpName = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Handle different file types
    switch ($fileExt) {
        case 'csv':
            $imported = importCSV($pdo, $tmpName, $importType);
            break;
        
        case 'xlsx':
        case 'xls':
            $imported = importExcel($pdo, $tmpName, $importType);
            break;
        
        case 'sql':
            $imported = importSQL($pdo, $tmpName);
            break;
        
        default:
            throw new Exception('Unsupported file format');
    }
    
    echo json_encode([
        'success' => true,
        'imported' => $imported,
        'message' => "Successfully imported $imported records"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function importCSV($pdo, $filePath, $importType) {
    $imported = 0;
    
    if (($handle = fopen($filePath, 'r')) !== false) {
        // Read header row
        $headers = fgetcsv($handle);
        
        // Normalize headers (lowercase, replace spaces with underscores)
        $headers = array_map(function($h) {
            return strtolower(str_replace(' ', '_', trim($h)));
        }, $headers);
        
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($headers)) continue;
            
            $row = array_combine($headers, $data);
            
            try {
                if ($importType === 'patients') {
                    importPatientRow($pdo, $row);
                } elseif ($importType === 'appointments') {
                    importAppointmentRow($pdo, $row);
                }
                $imported++;
            } catch (Exception $e) {
                // Log error but continue
                error_log("Import error: " . $e->getMessage());
            }
        }
        fclose($handle);
    }
    
    return $imported;
}

function importExcel($pdo, $filePath, $importType) {
    // Check if PhpSpreadsheet is available
    if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
        // Fallback: Try to read as CSV
        return importCSV($pdo, $filePath, $importType);
    }
    
    require_once '../vendor/autoload.php';
    
    $imported = 0;
    
    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        if (empty($rows)) return 0;
        
        // Get headers from first row
        $headers = array_map(function($h) {
            return strtolower(str_replace(' ', '_', trim($h)));
        }, $rows[0]);
        
        // Process data rows
        for ($i = 1; $i < count($rows); $i++) {
            if (count($rows[$i]) !== count($headers)) continue;
            
            $row = array_combine($headers, $rows[$i]);
            
            try {
                if ($importType === 'patients') {
                    importPatientRow($pdo, $row);
                } elseif ($importType === 'appointments') {
                    importAppointmentRow($pdo, $row);
                }
                $imported++;
            } catch (Exception $e) {
                error_log("Import error: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        throw new Exception("Excel import failed: " . $e->getMessage());
    }
    
    return $imported;
}

function importSQL($pdo, $filePath) {
    $sql = file_get_contents($filePath);
    
    if (empty($sql)) {
        throw new Exception('SQL file is empty');
    }
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    $executed = 0;
    
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $executed++;
        } catch (PDOException $e) {
            error_log("SQL import error: " . $e->getMessage());
        }
    }
    
    return $executed;
}

function importPatientRow($pdo, $row) {
    $patientType = $row['patient_type'] ?? $row['type'] ?? 'Student';
    
    if ($patientType === 'Student') {
        $data = [
            ':first_name' => $row['first_name'] ?? $row['firstname'] ?? '',
            ':last_name' => $row['last_name'] ?? $row['lastname'] ?? '',
            ':age' => $row['age'] ?? null,
            ':gender' => $row['gender'] ?? null,
            ':phone' => $row['phone'] ?? $row['contact'] ?? null,
            ':email' => $row['email'] ?? null,
            ':address' => $row['address'] ?? null,
            ':student_id' => $row['student_id'] ?? $row['studentid'] ?? null,
            ':education_level' => $row['education_level'] ?? $row['educationlevel'] ?? 'College',
            ':grade_level' => $row['grade_level'] ?? $row['gradelevel'] ?? '1st Year',
            ':course_track' => $row['course_track'] ?? $row['coursetrack'] ?? $row['course'] ?? 'General Education'
        ];
        
        $sql = "INSERT INTO students (
            first_name, last_name, age, gender, phone, email, address,
            student_id, education_level, grade_level, course_track
        ) VALUES (
            :first_name, :last_name, :age, :gender, :phone, :email, :address,
            :student_id, :education_level, :grade_level, :course_track
        )";
    } else { // Employee
        $data = [
            ':first_name' => $row['first_name'] ?? $row['firstname'] ?? '',
            ':last_name' => $row['last_name'] ?? $row['lastname'] ?? '',
            ':age' => $row['age'] ?? null,
            ':gender' => $row['gender'] ?? null,
            ':phone' => $row['phone'] ?? $row['contact'] ?? null,
            ':email' => $row['email'] ?? null,
            ':address' => $row['address'] ?? null,
            ':employee_id' => $row['employee_id'] ?? $row['employeeid'] ?? null,
            ':employee_type' => $row['employee_type'] ?? $row['employeetype'] ?? 'Staff',
            ':department' => $row['department'] ?? 'General'
        ];
        
        $sql = "INSERT INTO employees (
            first_name, last_name, age, gender, phone, email, address,
            employee_id, employee_type, department
        ) VALUES (
            :first_name, :last_name, :age, :gender, :phone, :email, :address,
            :employee_id, :employee_type, :department
        )";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}

function importAppointmentRow($pdo, $row) {
    // Find or create patient
    $patientId = null;
    $patientType = null;
    
    if (!empty($row['patient_id']) && !empty($row['patient_type'])) {
        $patientId = $row['patient_id'];
        $patientType = $row['patient_type'];
    } else {
        // Try to find patient by name
        $firstName = $row['first_name'] ?? $row['firstname'] ?? '';
        $lastName = $row['last_name'] ?? $row['lastname'] ?? '';
        
        if (!empty($firstName) && !empty($lastName)) {
            // Try students first
            $stmt = $pdo->prepare("
                SELECT id FROM students 
                WHERE first_name = :first_name AND last_name = :last_name AND is_deleted = 0
                LIMIT 1
            ");
            $stmt->execute([':first_name' => $firstName, ':last_name' => $lastName]);
            $student = $stmt->fetch();
            
            if ($student) {
                $patientId = $student['id'];
                $patientType = 'Student';
            } else {
                // Try employees
                $stmt = $pdo->prepare("
                    SELECT id FROM employees 
                    WHERE first_name = :first_name AND last_name = :last_name AND is_deleted = 0
                    LIMIT 1
                ");
                $stmt->execute([':first_name' => $firstName, ':last_name' => $lastName]);
                $employee = $stmt->fetch();
                
                if ($employee) {
                    $patientId = $employee['id'];
                    $patientType = 'Employee';
                }
            }
        }
    }
    
    if (!$patientId || !$patientType) {
        throw new Exception('Patient not found for appointment');
    }
    
    $data = [
        ':patient_type' => $patientType,
        ':patient_id' => $patientId,
        ':appointment_date' => $row['appointment_date'] ?? $row['date'] ?? date('Y-m-d'),
        ':appointment_time' => $row['appointment_time'] ?? $row['time'] ?? '09:00:00',
        ':appointment_type' => $row['appointment_type'] ?? $row['type'] ?? 'Regular Checkup',
        ':status' => $row['status'] ?? 'pending',
        ':notes' => $row['notes'] ?? null
    ];
    
    $sql = "INSERT INTO appointments (
        patient_type, patient_id, appointment_date, appointment_time, appointment_type, status, notes
    ) VALUES (
        :patient_type, :patient_id, :appointment_date, :appointment_time, :appointment_type, :status, :notes
    )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}
?>
