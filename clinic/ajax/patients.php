<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'getPatients': getPatients($pdo); break;
        case 'getPatient': getPatient($pdo); break;
        case 'addPatient': addPatient($pdo); break;
        case 'updatePatient': updatePatient($pdo); break;
        case 'deletePatient': deletePatient($pdo); break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

# ==========================
# GET PATIENTS
# ==========================
function getPatients($pdo) {
    $type = $_POST['type'] ?? '';
    $search = $_POST['search'] ?? '';

    $patients = [];

    # STUDENTS
    if ($type === '' || $type === 'Student') {
        $sql = "SELECT 
                    'Student' AS patient_type,
                    Student_id AS id,
                    first_name,
                    last_name,
                    age,
                    gender,
                    mobile_no,
                    email,
                    class,
                    education_lvl,
                    year_lvl,
                    shs_strand,
                    program
                FROM Students
                WHERE is_deleted = 0";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = array_merge($patients, $stmt->fetchAll());
    }

    # EMPLOYEES
    if ($type === '' || $type === 'Employee') {
        $sql = "SELECT 
                    'Employee' AS patient_type,
                    employee_id AS id,
                    first_name,
                    last_name,
                    age,
                    gender,
                    mobile_no,
                    email,
                    class,
                    department
                FROM Employees
                WHERE is_deleted = 0";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = array_merge($patients, $stmt->fetchAll());
    }

    usort($patients, fn($a, $b) => strcmp($a['first_name'], $b['first_name']));

    echo json_encode(['success' => true, 'patients' => $patients]);
}

# ==========================
# GET SINGLE PATIENT
# ==========================
function getPatient($pdo) {
    $id = $_POST['id'] ?? 0;
    $type = $_POST['type'] ?? '';

    if ($type === 'Student') {
        $stmt = $pdo->prepare("SELECT *, 'Student' AS patient_type FROM Students WHERE Student_id = :id AND is_deleted = 0");
    } else {
        $stmt = $pdo->prepare("SELECT *, 'Employee' AS patient_type FROM Employees WHERE employee_id = :id AND is_deleted = 0");
    }

    $stmt->execute([':id' => $id]);
    $patient = $stmt->fetch();

    echo json_encode(['success' => (bool)$patient, 'patient' => $patient]);
}

# ==========================
# ADD PATIENT
# ==========================
function addPatient($pdo) {
    $type = $_POST['patientType'] ?? '';

    if ($type === 'Student') {
        $sql = "INSERT INTO Students 
            (first_name, last_name, age, gender, mobile_no, email, class, education_lvl, year_lvl, shs_strand, program, address_record)
            VALUES 
            (:first_name, :last_name, :age, :gender, :mobile_no, :email, :class, :education_lvl, :year_lvl, :shs_strand, :program, :address)";

        $data = [
            ':first_name' => $_POST['firstName'],
            ':last_name' => $_POST['lastName'],
            ':age' => $_POST['age'],
            ':gender' => $_POST['gender'],
            ':mobile_no' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':class' => $_POST['class'],
            ':education_lvl' => $_POST['educationLevel'],
            ':year_lvl' => $_POST['gradeLevel'],
            ':shs_strand' => $_POST['strand'] ?? null,
            ':program' => $_POST['program'] ?? null,
            ':address' => $_POST['address']
        ];
    } else {
        $sql = "INSERT INTO Employees
            (first_name, last_name, age, gender, mobile_no, email, class, department, address_record)
            VALUES
            (:first_name, :last_name, :age, :gender, :mobile_no, :email, :class, :department, :address)";

        $data = [
            ':first_name' => $_POST['firstName'],
            ':last_name' => $_POST['lastName'],
            ':age' => $_POST['age'],
            ':gender' => $_POST['gender'],
            ':mobile_no' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':class' => $_POST['class'],
            ':department' => $_POST['department'],
            ':address' => $_POST['address']
        ];
    }

    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute($data);

    echo json_encode(['success' => $success]);
}

# ==========================
# UPDATE PATIENT
# ==========================
function updatePatient($pdo) {
    $id = $_POST['patientId'];
    $type = $_POST['patientType'];

    if ($type === 'Student') {
        $sql = "UPDATE Students SET 
            first_name = :first_name,
            last_name = :last_name,
            age = :age,
            gender = :gender,
            mobile_no = :mobile_no,
            email = :email,
            class = :class,
            education_lvl = :education_lvl,
            year_lvl = :year_lvl,
            shs_strand = :shs_strand,
            program = :program,
            address_record = :address
            WHERE Student_id = :id";
    } else {
        $sql = "UPDATE Employees SET
            first_name = :first_name,
            last_name = :last_name,
            age = :age,
            gender = :gender,
            mobile_no = :mobile_no,
            email = :email,
            class = :class,
            department = :department,
            address_record = :address
            WHERE employee_id = :id";
    }

    $data = [
        ':id' => $id,
        ':first_name' => $_POST['firstName'],
        ':last_name' => $_POST['lastName'],
        ':age' => $_POST['age'],
        ':gender' => $_POST['gender'],
        ':mobile_no' => $_POST['phone'],
        ':email' => $_POST['email'],
        ':class' => $_POST['class'],
        ':education_lvl' => $_POST['educationLevel'] ?? null,
        ':year_lvl' => $_POST['gradeLevel'] ?? null,
        ':shs_strand' => $_POST['strand'] ?? null,
        ':program' => $_POST['program'] ?? null,
        ':department' => $_POST['department'] ?? null,
        ':address' => $_POST['address']
    ];

    $stmt = $pdo->prepare($sql);
    echo json_encode(['success' => $stmt->execute($data)]);
}

# ==========================
# DELETE PATIENT
# ==========================
function deletePatient($pdo) {
    $id = $_POST['id'];
    $type = $_POST['type'];

    if ($type === 'Student') {
        $stmt = $pdo->prepare("UPDATE Students SET is_deleted = 1 WHERE Student_id = :id");
    } else {
        $stmt = $pdo->prepare("UPDATE Employees SET is_deleted = 1 WHERE employee_id = :id");
    }

    $stmt->execute([':id' => $id]);

    $stmt = $pdo->prepare("UPDATE appointments SET is_deleted = 1 WHERE patient_id = :id AND patient_type = :type");
    $stmt->execute([':id' => $id, ':type' => $type]);

    echo json_encode(['success' => true]);
}
?>