-- Migration Script: Convert old single patients table to separate students/employees tables
-- Use this ONLY if you have an existing database with the old structure
-- For fresh installations, use database.sql instead

USE fcpc_clinic;

-- Step 1: Create new tables if they don't exist
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    student_id VARCHAR(50) UNIQUE,
    education_level ENUM('Elementary', 'Junior High', 'Senior High', 'College') NOT NULL,
    grade_level VARCHAR(50) NOT NULL,
    course_track VARCHAR(100) NOT NULL,
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    INDEX idx_education_level (education_level),
    INDEX idx_name (first_name, last_name),
    INDEX idx_deleted (is_deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    employee_id VARCHAR(50) UNIQUE,
    employee_type ENUM('Teacher', 'Staff', 'Administration') NOT NULL,
    department VARCHAR(100) NOT NULL,
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    INDEX idx_employee_type (employee_type),
    INDEX idx_name (first_name, last_name),
    INDEX idx_deleted (is_deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Migrate data from old patients table (if it exists)
-- Check if patients table exists
SET @tableExists = (SELECT COUNT(*) FROM information_schema.tables 
                    WHERE table_schema = 'fcpc_clinic' AND table_name = 'patients');

-- Migrate students
INSERT INTO students (first_name, last_name, age, gender, phone, email, address, 
                     student_id, education_level, grade_level, course_track, 
                     date_added, last_updated, is_deleted)
SELECT first_name, last_name, age, gender, phone, email, address,
       student_id, 
       COALESCE(education_level, 'College'),
       COALESCE(grade_level, '1st Year'),
       COALESCE(course_track, 'General Education'),
       date_added, last_updated, is_deleted
FROM patients
WHERE patient_type = 'Student'
ON DUPLICATE KEY UPDATE id=id;

-- Migrate employees
INSERT INTO employees (first_name, last_name, age, gender, phone, email, address,
                      employee_id, employee_type, department,
                      date_added, last_updated, is_deleted)
SELECT first_name, last_name, age, gender, phone, email, address,
       employee_id,
       COALESCE(employee_type, 'Staff'),
       COALESCE(department, 'General'),
       date_added, last_updated, is_deleted
FROM patients
WHERE patient_type = 'Employee'
ON DUPLICATE KEY UPDATE id=id;

-- Step 3: Update appointments table structure
ALTER TABLE appointments 
ADD COLUMN IF NOT EXISTS patient_type ENUM('Student', 'Employee') NOT NULL DEFAULT 'Student' AFTER id;

-- Update existing appointments with patient_type based on old patients table
UPDATE appointments a
JOIN patients p ON a.patient_id = p.id
SET a.patient_type = p.patient_type
WHERE EXISTS (SELECT 1 FROM patients WHERE id = a.patient_id);

-- Step 4: Drop the old trigger if it exists
DROP TRIGGER IF EXISTS after_appointment_complete;

-- Step 5: Create new trigger for separate tables
DELIMITER $$

CREATE TRIGGER after_appointment_complete
AFTER UPDATE ON appointments
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        IF NEW.patient_type = 'Student' THEN
            INSERT INTO appointment_records 
                (patient_type, patient_id, patient_name, patient_gender, education_level, 
                 grade_level, course_track, employee_type, department, appointment_date, 
                 appointment_time, appointment_type, status, notes, record_year)
            SELECT 
                'Student',
                s.id,
                CONCAT(s.first_name, ' ', s.last_name),
                s.gender,
                s.education_level,
                s.grade_level,
                s.course_track,
                NULL,
                NULL,
                NEW.appointment_date,
                NEW.appointment_time,
                NEW.appointment_type,
                NEW.status,
                NEW.notes,
                YEAR(NEW.appointment_date)
            FROM students s
            WHERE s.id = NEW.patient_id;
        ELSE
            INSERT INTO appointment_records 
                (patient_type, patient_id, patient_name, patient_gender, education_level, 
                 grade_level, course_track, employee_type, department, appointment_date, 
                 appointment_time, appointment_type, status, notes, record_year)
            SELECT 
                'Employee',
                e.id,
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
            FROM employees e
            WHERE e.id = NEW.patient_id;
        END IF;
    END IF;
END$$

DELIMITER ;

-- Step 6: Backup and rename old patients table (DO NOT DROP - for safety)
RENAME TABLE patients TO patients_backup_old;

-- Migration complete!
-- The old patients table is now renamed to patients_backup_old
-- You can drop it later after verifying everything works:
-- DROP TABLE patients_backup_old;
