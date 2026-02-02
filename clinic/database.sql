-- ============================================================
-- FIRST CITY PROVIDENTIAL COLLEGE - CLINIC MANAGEMENT SYSTEM
-- ============================================================

CREATE DATABASE IF NOT EXISTS fcpc_clinic
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fcpc_clinic;

-- ============================================================
-- MEDICAL RECORDS TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS medical_records(
    medical_record_id INT AUTO_INCREMENT PRIMARY KEY,
    record_date DATE NOT NULL,
    record_time TIME NOT NULL,
    bp VARCHAR(10),
    hr INT,
    rr INT,
    osat DECIMAL(5,2),
    temp DECIMAL(4,1),
    height_record DECIMAL(5,2),
    weight_record DECIMAL(5,2),
    bmi DECIMAL(4,1),
    prior_visit TEXT,
    present_visit TEXT,
    intervention TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- EMPLOYEES TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS Employees(
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    mobile_no VARCHAR(20),
    email VARCHAR(100),
    employee_type VARCHAR(50) NOT NULL,
    department ENUM('Registrar', 'Faculty', 'ICT', 'HR', 'Clinic') NOT NULL,
    address_record TEXT,
    is_deleted TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- STUDENTS TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS Students(
    Student_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    class VARCHAR(50) NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    education_lvl ENUM('K-10', 'Senior High', 'College') NOT NULL,
    year_lvl VARCHAR(20),
    shs_strand ENUM('STEM', 'ABM', 'HUMSS', 'GAS', 'TVL'),
    program ENUM('BSED', 'BSIT', 'BSBA', 'BSHRM', 'BSCRIM'),
    mobile_no VARCHAR(20),
    email VARCHAR(100),
    address_record TEXT,
    is_deleted TINYINT(1) DEFAULT 0,

    CONSTRAINT chk_year_lvl CHECK (
        (education_lvl = 'K-10' AND year_lvl IN (
            'Kinder','Grade 1','Grade 2','Grade 3','Grade 4',
            'Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10'
        ))
        OR
        (education_lvl = 'Senior High' AND year_lvl IN ('Grade 11','Grade 12'))
        OR
        (education_lvl = 'College' AND year_lvl IN (
            '1st Year','2nd Year','3rd Year','4th Year','5th Year'
        ))
    ),

    CONSTRAINT chk_program CHECK (
        (education_lvl = 'College' AND program IS NOT NULL AND shs_strand IS NULL)
        OR
        (education_lvl = 'Senior High' AND shs_strand IS NOT NULL AND program IS NULL)
        OR
        (education_lvl = 'K-10' AND shs_strand IS NULL AND program IS NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- APPOINTMENTS TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_type ENUM('Student', 'Employee') NOT NULL,
    patient_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    appointment_type VARCHAR(100),
    status ENUM('pending', 'confirmed', 'checked-in', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    INDEX idx_patient (patient_type, patient_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_deleted (is_deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- APPOINTMENT RECORDS ARCHIVE TABLE (20 YEARS)
-- ============================================================

CREATE TABLE IF NOT EXISTS appointment_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_type ENUM('Student', 'Employee') NOT NULL,
    patient_id INT NOT NULL,
    patient_name VARCHAR(200),
    patient_gender ENUM('Male', 'Female', 'Other'),
    education_level VARCHAR(50),
    grade_level VARCHAR(50),
    course_track VARCHAR(100),
    employee_type VARCHAR(50),
    department VARCHAR(100),
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    appointment_type VARCHAR(100),
    status VARCHAR(50),
    notes TEXT,
    record_year INT NOT NULL,
    archived_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_record_year (record_year),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_patient_type (patient_type),
    INDEX idx_education_level (education_level),
    INDEX idx_employee_type (employee_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SYSTEM SETTINGS TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO system_settings (setting_key, setting_value) VALUES
('clinic_name', 'First City Providential College Clinic'),
('record_retention_years', '20'),
('notification_enabled', '1')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- ============================================================
-- TRIGGER: AUTO-ARCHIVE COMPLETED APPOINTMENTS
-- ============================================================

DELIMITER $$

CREATE TRIGGER after_appointment_complete
AFTER UPDATE ON appointments
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN

        -- STUDENT ARCHIVE
        IF NEW.patient_type = 'Student' THEN
            INSERT INTO appointment_records (
                patient_type, patient_id, patient_name, patient_gender,
                education_level, grade_level, course_track,
                employee_type, department,
                appointment_date, appointment_time,
                appointment_type, status, notes, record_year
            )
            SELECT 
                'Student',
                s.Student_id,
                CONCAT(s.first_name, ' ', s.last_name),
                s.gender,
                s.education_lvl,
                s.year_lvl,
                COALESCE(s.program, s.shs_strand),
                NULL,
                NULL,
                NEW.appointment_date,
                NEW.appointment_time,
                NEW.appointment_type,
                NEW.status,
                NEW.notes,
                YEAR(NEW.appointment_date)
            FROM Students s
            WHERE s.Student_id = NEW.patient_id;

        -- EMPLOYEE ARCHIVE
        ELSE
            INSERT INTO appointment_records (
                patient_type, patient_id, patient_name, patient_gender,
                education_level, grade_level, course_track,
                employee_type, department,
                appointment_date, appointment_time,
                appointment_type, status, notes, record_year
            )
            SELECT 
                'Employee',
                e.employee_id,
                CONCAT(e.first_name, ' ', e.last_name),
                e.gender,
                NULL,
                NULL,
                NULL,
                e.employee_type,
                e.department,
                NEW.appointment_date,
                NEW.appointment_time,
                NEW.appointment_type,
                NEW.status,
                NEW.notes,
                YEAR(NEW.appointment_date)
            FROM Employees e
            WHERE e.employee_id = NEW.patient_id;
        END IF;

    END IF;
END$$

DELIMITER ;