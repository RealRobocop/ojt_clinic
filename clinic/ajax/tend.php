<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'getMedicalRecords':
        getMedicalRecords($pdo);
        break;

    case 'addMedicalRecord':
        addMedicalRecord($pdo);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}


/**
 * FETCH MEDICAL RECORDS (TEND LIST)
 */
function getMedicalRecords($pdo) {
    $search = $_POST['search'] ?? '';

    $sql = "
        SELECT m.*, 
        CASE 
            WHEN m.patient_type = 'Student' THEN CONCAT(s.first_name, ' ', s.last_name)
            WHEN m.patient_type = 'Employee' THEN CONCAT(e.first_name, ' ', e.last_name)
        END AS patient_name
        FROM medical_records m
        LEFT JOIN students s 
            ON m.patient_id = s.Student_id AND m.patient_type = 'Student'
        LEFT JOIN employees e 
            ON m.patient_id = e.employee_id AND m.patient_type = 'Employee'
    ";

    $params = [];

    if ($search) {
        $sql .= " WHERE 
            CONCAT(s.first_name, ' ', s.last_name) LIKE :search
            OR CONCAT(e.first_name, ' ', e.last_name) LIKE :search
        ";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY m.record_date DESC, m.record_time DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll();

    echo json_encode(['success' => true, 'records' => $records]);
}

/**
 * ADD MEDICAL RECORD
 */
function addMedicalRecord($pdo) {
    $sql = "INSERT INTO medical_records (
        appointment_id, patient_id, patient_type,
        bp, hr, rr, osat, temp,
        height_record, weight_record, bmi,
        prior_visit, present_visit, intervention,
        record_date, record_time
    ) VALUES (
        :appointment_id, :patient_id, :patient_type,
        :bp, :hr, :rr, :osat, :temp,
        :height, :weight, :bmi,
        :prior_visit, :present_visit, :intervention,
        CURDATE(), CURTIME()
    )";

    $stmt = $pdo->prepare($sql);

    $success = $stmt->execute([
        ':appointment_id' => $_POST['appointmentId'] ?? null,
        ':patient_id' => $_POST['patientId'],
        ':patient_type' => $_POST['patientType'],
        ':bp' => $_POST['bp'] ?? null,
        ':hr' => $_POST['hr'] ?? null,
        ':rr' => $_POST['rr'] ?? null,
        ':osat' => $_POST['osat'] ?? null,
        ':temp' => $_POST['temp'] ?? null,
        ':height' => $_POST['height'] ?? null,
        ':weight' => $_POST['weight'] ?? null,
        ':bmi' => $_POST['bmi'] ?? null,
        ':prior_visit' => $_POST['priorVisit'] ?? null,
        ':present_visit' => $_POST['presentVisit'] ?? null,
        ':intervention' => $_POST['intervention'] ?? null
    ]);

    if (empty($_POST['patientId']) || empty($_POST['patientType'])) {
    echo json_encode(['success' => false, 'message' => 'Missing patient information']);
    return;
    }

    echo json_encode(['success' => $success]);
}