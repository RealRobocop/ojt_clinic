<?php
/**
 * First City Providential College - Clinic Management System
 * Records AJAX Handler
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
        case 'getRecords':
            getRecords($pdo);
            break;
        
        case 'getStatistics':
            getStatistics($pdo);
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getRecords($pdo) {
    $year = $_POST['year'] ?? '';
    $patientType = $_POST['patientType'] ?? '';
    
    $sql = "SELECT * FROM appointment_records WHERE 1=1";
    $params = [];
    
    if (!empty($year)) {
        $sql .= " AND record_year = :year";
        $params[':year'] = $year;
    }
    
    if (!empty($patientType)) {
        $sql .= " AND patient_type = :patient_type";
        $params[':patient_type'] = $patientType;
    }
    
    $sql .= " ORDER BY appointment_date DESC, appointment_time DESC LIMIT 500";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'records' => $records]);
}

function getStatistics($pdo) {
    $statistics = [];
    
    // Gender distribution
    $stmt = $pdo->query("
        SELECT patient_gender, COUNT(*) as count
        FROM appointment_records
        WHERE patient_gender IS NOT NULL
        GROUP BY patient_gender
    ");
    $genderData = [];
    while ($row = $stmt->fetch()) {
        $genderData[$row['patient_gender']] = (int)$row['count'];
    }
    $statistics['gender'] = $genderData;
    
    // Education level distribution (for students)
    $stmt = $pdo->query("
        SELECT education_level, COUNT(*) as count
        FROM appointment_records
        WHERE patient_type = 'Student' AND education_level IS NOT NULL
        GROUP BY education_level
        ORDER BY count DESC
    ");
    $educationData = [];
    while ($row = $stmt->fetch()) {
        $educationData[$row['education_level']] = (int)$row['count'];
    }
    $statistics['education'] = $educationData;
    
    // Employee type distribution
    $stmt = $pdo->query("
        SELECT employee_type, COUNT(*) as count
        FROM appointment_records
        WHERE patient_type = 'Employee' AND employee_type IS NOT NULL
        GROUP BY employee_type
        ORDER BY count DESC
    ");
    $employeeData = [];
    while ($row = $stmt->fetch()) {
        $employeeData[$row['employee_type']] = (int)$row['count'];
    }
    $statistics['employee'] = $employeeData;
    
    // Course/Track distribution
    $stmt = $pdo->query("
        SELECT course_track, COUNT(*) as count
        FROM appointment_records
        WHERE course_track IS NOT NULL
        GROUP BY course_track
        ORDER BY count DESC
        LIMIT 10
    ");
    $courseData = [];
    while ($row = $stmt->fetch()) {
        $courseData[$row['course_track']] = (int)$row['count'];
    }
    $statistics['courses'] = $courseData;
    
    echo json_encode(['success' => true, 'statistics' => $statistics]);
}
?>
