<?php
/**
 * First City Providential College - Clinic Management System
 * Export Handler - Supports Excel, CSV, SQL
 */

require_once '../includes/db.php';

$exportType = $_GET['exportType'] ?? '';
$exportFormat = $_GET['exportFormat'] ?? 'excel';
$yearFrom = $_GET['exportYearFrom'] ?? '';
$yearTo = $_GET['exportYearTo'] ?? '';

try {
    $data = [];
    $filename = '';
    
    switch ($exportType) {
        case 'patients':
            $data = exportPatients($pdo);
            $filename = 'fcpc_patients_' . date('Y-m-d');
            break;
        
        case 'appointments':
            $data = exportAppointments($pdo, $yearFrom, $yearTo);
            $filename = 'fcpc_appointments_' . date('Y-m-d');
            break;
        
        case 'records':
            $data = exportRecords($pdo, $yearFrom, $yearTo);
            $filename = 'fcpc_records_' . date('Y-m-d');
            break;
        
        default:
            throw new Exception('Invalid export type');
    }
    
    if (empty($data)) {
        throw new Exception('No data to export');
    }
    
    switch ($exportFormat) {
        case 'csv':
            exportCSV($data, $filename);
            break;
        
        case 'excel':
            exportExcel($data, $filename);
            break;
        
        case 'sql':
            exportSQL($data, $filename, $exportType);
            break;
        
        default:
            throw new Exception('Invalid export format');
    }
    
} catch (Exception $e) {
    die('Export Error: ' . $e->getMessage());
}

function exportPatients($pdo) {
    // Get students
    $stmtStudents = $pdo->query("
        SELECT 
            'Student' as patient_type,
            first_name, last_name, age, gender, phone, email, address,
            student_id, education_level, grade_level, course_track,
            NULL as employee_id, NULL as employee_type, NULL as department,
            date_added
        FROM students 
        WHERE is_deleted = 0
        ORDER BY first_name ASC, last_name ASC
    ");
    $students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
    
    // Get employees
    $stmtEmployees = $pdo->query("
        SELECT 
            'Employee' as patient_type,
            first_name, last_name, age, gender, phone, email, address,
            employee_id, employee_type, department,
            NULL as student_id, NULL as education_level, NULL as grade_level, NULL as course_track,
            date_added
        FROM employees 
        WHERE is_deleted = 0
        ORDER BY first_name ASC, last_name ASC
    ");
    $employees = $stmtEmployees->fetchAll(PDO::FETCH_ASSOC);
    
    // Merge both arrays
    return array_merge($students, $employees);
}

function exportAppointments($pdo, $yearFrom, $yearTo) {
    $sql = "SELECT 
                a.id, a.patient_type, a.patient_id,
                CASE 
                    WHEN a.patient_type = 'Student' THEN CONCAT(s.first_name, ' ', s.last_name)
                    WHEN a.patient_type = 'Employee' THEN CONCAT(e.first_name, ' ', e.last_name)
                END as patient_name,
                a.patient_type,
                a.appointment_date, a.appointment_time, a.appointment_type,
                a.status, a.notes, a.created_at
            FROM appointments a
            LEFT JOIN students s ON a.patient_id = s.id AND a.patient_type = 'Student'
            LEFT JOIN employees e ON a.patient_id = e.id AND a.patient_type = 'Employee'
            WHERE a.is_deleted = 0";
    
    $params = [];
    
    if (!empty($yearFrom)) {
        $sql .= " AND YEAR(a.appointment_date) >= :year_from";
        $params[':year_from'] = $yearFrom;
    }
    
    if (!empty($yearTo)) {
        $sql .= " AND YEAR(a.appointment_date) <= :year_to";
        $params[':year_to'] = $yearTo;
    }
    
    $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function exportRecords($pdo, $yearFrom, $yearTo) {
    $sql = "SELECT * FROM appointment_records WHERE 1=1";
    $params = [];
    
    if (!empty($yearFrom)) {
        $sql .= " AND record_year >= :year_from";
        $params[':year_from'] = $yearFrom;
    }
    
    if (!empty($yearTo)) {
        $sql .= " AND record_year <= :year_to";
        $params[':year_to'] = $yearTo;
    }
    
    $sql .= " ORDER BY appointment_date DESC, appointment_time DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function exportCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Write headers
    fputcsv($output, array_keys($data[0]));
    
    // Write data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
}

function exportExcel($data, $filename) {
    // Simple Excel export using HTML table (works in Excel)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta charset="UTF-8"></head>';
    echo '<body>';
    echo '<table border="1">';
    
    // Headers
    echo '<tr>';
    foreach (array_keys($data[0]) as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    echo '</tr>';
    
    // Data
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td>' . htmlspecialchars($cell ?? '') . '</td>';
        }
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</body></html>';
}

function exportSQL($data, $filename, $exportType) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '.sql"');
    
    $tableName = '';
    switch ($exportType) {
        case 'patients':
            $tableName = 'patients';
            break;
        case 'appointments':
            $tableName = 'appointments';
            break;
        case 'records':
            $tableName = 'appointment_records';
            break;
    }
    
    echo "-- FCPC Clinic Management System Export\n";
    echo "-- Export Date: " . date('Y-m-d H:i:s') . "\n";
    echo "-- Table: $tableName\n\n";
    
    foreach ($data as $row) {
        $columns = array_keys($row);
        $values = array_map(function($val) {
            if ($val === null) return 'NULL';
            return "'" . addslashes($val) . "'";
        }, array_values($row));
        
        echo "INSERT INTO $tableName (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
}
?>
