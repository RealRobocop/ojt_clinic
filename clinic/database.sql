-- First City Providential College - Clinic Management System
-- Database Schema with Separate Student and Employee Tables

CREATE DATABASE IF NOT EXISTS fcpc_clinic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fcpc_clinic;

-- medical_records Table
CREATE TABLE IF NOT EXISTS  medical_records(
    id INT AUTO_INCREMENT PRIMARY KEY,
    record_date DATE NOT NULL,
    record_time TIME NOT NULL,
    bp VARCHAR(10),          -- e.g. 120/80
    hr INT,                  -- heart rate
    rr INT,                  -- respiratory rate
    osat DECIMAL(5,2),       -- oxygen saturation (%)
    temp DECIMAL(4,1),       -- temperature
    height_record DECIMAL(5,2),     -- cm or inches
    weight_record DECIMAL(5,2),     -- kg or lbs
    bmi DECIMAL(4,1),
    prior_visit TEXT,
    present_visit TEXT,
    intervention TEXT,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employees Table
CREATE TABLE IF NOT EXISTS  (
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

-- Appointments Table (links to both students and employees)
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

-- Appointment Records (Historical Archive - 20 years)
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

-- Medical Records Table
CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_type ENUM('Student', 'Employee') NOT NULL,
    patient_id INT NOT NULL,
    appointment_id INT,
    diagnosis TEXT,
    treatment TEXT,
    medications TEXT,
    vital_signs JSON,
    doctor_notes TEXT,
    record_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    INDEX idx_patient (patient_type, patient_id),
    INDEX idx_record_date (record_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System Settings Table
CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO system_settings (setting_key, setting_value) VALUES
('clinic_name', 'First City Providential College Clinic'),
('record_retention_years', '20'),
('notification_enabled', '1')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- Trigger to auto-archive completed appointments to records
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
