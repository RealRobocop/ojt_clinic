-- ========================================
-- CLINIC MANAGEMENT SYSTEM - SQL DATABASE
-- ========================================
-- Complete database schema with soft delete and doctor authentication

-- ========================================
-- CREATE DATABASE
-- ========================================
-- CREATE DATABASE clinic_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE clinic_management;

-- ========================================
-- TABLE 0: DOCTOR ACCOUNTS - LOGIN CREDENTIALS
-- ========================================
/**
 * Stores doctor login credentials
 * Username: Doctor1
 * Password: Test123
 */
CREATE TABLE IF NOT EXISTS doctor_accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    firstName VARCHAR(100),
    lastName VARCHAR(100),
    specialization VARCHAR(100),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lastLogin TIMESTAMP NULL,
    INDEX idx_username (username)
);

-- ========================================
-- INSERT DEFAULT DOCTOR ACCOUNT
-- ========================================
-- Username: Doctor1
-- Password: Test123 (stored as plain text for testing - change in production!)
-- For production, use PHP: password_hash('Test123', PASSWORD_BCRYPT)
INSERT INTO doctor_accounts (username, password, firstName, lastName, specialization) VALUES
('Doctor1', 'Test123', 'John', 'Smith', 'General Practice');

-- ========================================
-- TABLE 1: PATIENTS
-- ========================================
/**
 * Stores patient information with soft delete
 */
CREATE TABLE IF NOT EXISTS patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL CHECK (age >= 0 AND age <= 150),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address VARCHAR(255),
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'When record was deleted',
    dateAdded DATE NOT NULL DEFAULT CURDATE(),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_dateAdded (dateAdded),
    INDEX idx_is_deleted (is_deleted)
);

-- ========================================
-- TABLE 1B: MASTER PATIENTS - AUDIT LOG
-- ========================================
/**
 * Master patients table - keeps all records including deleted ones
 * For record keeping and audit trail
 */
CREATE TABLE IF NOT EXISTS Master_patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL CHECK (age >= 0 AND age <= 150),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address VARCHAR(255),
    dateAdded DATE NOT NULL DEFAULT CURDATE(),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_dateAdded (dateAdded)
);

-- ========================================
-- TABLE 2: APPOINTMENTS
-- ========================================
/**
 * Stores appointment information with soft delete
 */
CREATE TABLE IF NOT EXISTS appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patientId INT NOT NULL,
    patientName VARCHAR(100) NOT NULL,
    appointmentDate DATE NOT NULL,
    appointmentTime TIME NOT NULL,
    appointmentType ENUM('Regular Checkup', 'Follow-up Visit', 'Consultation', 'Vaccination') NOT NULL,
    status ENUM('pending', 'confirmed', 'checked-in', 'cancelled') NOT NULL DEFAULT 'pending',
    notes TEXT,
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'When record was deleted',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patientId) REFERENCES patients(id),
    INDEX idx_patientId (patientId),
    INDEX idx_date (appointmentDate),
    INDEX idx_status (status),
    INDEX idx_is_deleted (is_deleted)
);

-- ========================================
-- TABLE 2B: MASTER APPOINTMENTS - AUDIT LOG
-- ========================================
/**
 * Master appointments table - keeps all records including deleted ones
 * For record keeping and audit trail
 */
CREATE TABLE IF NOT EXISTS Master_appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patientId INT NOT NULL,
    patientName VARCHAR(100) NOT NULL,
    appointmentDate DATE NOT NULL,
    appointmentTime TIME NOT NULL,
    appointmentType ENUM('Regular Checkup', 'Follow-up Visit', 'Consultation', 'Vaccination') NOT NULL,
    status ENUM('pending', 'confirmed', 'checked-in', 'cancelled') NOT NULL DEFAULT 'pending',
    notes TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patientId (patientId),
    INDEX idx_date (appointmentDate),
    INDEX idx_status (status)
);

-- ========================================
-- SAMPLE DATA - PATIENTS
-- ========================================
INSERT INTO patients (name, age, gender, phone, email, address, dateAdded) VALUES
('Sarah Johnson', 34, 'Female', '(555) 123-4567', 'sarah.johnson@email.com', '123 Main St, City, State', '2024-01-15'),
('Michael Chen', 45, 'Male', '(555) 234-5678', 'michael.chen@email.com', '456 Oak Ave, City, State', '2024-02-10'),
('Emma Wilson', 28, 'Female', '(555) 345-6789', 'emma.wilson@email.com', '789 Pine Rd, City, State', '2024-03-05'),
('David Williams', 65, 'Male', '(555) 456-7890', 'david.williams@email.com', '321 Elm St, City, State', '2024-01-12'),
('Jennifer Brown', 42, 'Female', '(555) 567-8901', 'jennifer.brown@email.com', '654 Maple Dr, City, State', '2024-03-20'),
('James Martinez', 55, 'Male', '(555) 678-9012', 'james.martinez@email.com', '987 Birch Ln, City, State', '2024-02-28');

-- ========================================
-- SAMPLE DATA - APPOINTMENTS
-- ========================================
INSERT INTO appointments (patientId, patientName, appointmentDate, appointmentTime, appointmentType, status, notes) VALUES
(1, 'Sarah Johnson', CURDATE(), '14:30', 'Regular Checkup', 'confirmed', 'Annual checkup'),
(2, 'Michael Chen', CURDATE(), '15:00', 'Follow-up Visit', 'pending', 'Follow-up for blood pressure'),
(3, 'Emma Wilson', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00', 'Consultation', 'confirmed', ''),
(4, 'David Williams', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:30', 'Vaccination', 'pending', 'Flu vaccine'),
(5, 'Jennifer Brown', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '11:00', 'Regular Checkup', 'confirmed', 'Routine checkup');

-- ========================================
-- USEFUL QUERIES
-- ========================================

-- Get only active (non-deleted) patients
-- SELECT * FROM patients WHERE is_deleted = 0 ORDER BY dateAdded DESC;

-- Get only deleted patients
-- SELECT * FROM patients WHERE is_deleted = 1 ORDER BY deleted_at DESC;

-- Get only active (non-deleted) appointments
-- SELECT * FROM appointments WHERE is_deleted = 0 ORDER BY appointmentDate DESC;

-- Get only deleted appointments
-- SELECT * FROM appointments WHERE is_deleted = 1 ORDER BY deleted_at DESC;

-- Soft delete a patient
-- UPDATE patients SET is_deleted = 1, deleted_at = CURRENT_TIMESTAMP WHERE id = ?;

-- Restore a deleted patient
-- UPDATE patients SET is_deleted = 0, deleted_at = NULL WHERE id = ?;

-- Soft delete an appointment
-- UPDATE appointments SET is_deleted = 1, deleted_at = CURRENT_TIMESTAMP WHERE id = ?;

-- Restore a deleted appointment
-- UPDATE appointments SET is_deleted = 0, deleted_at = NULL WHERE id = ?;

-- ========================================
-- COMPLETE STATISTICS
-- ========================================
-- SELECT
--     (SELECT COUNT(*) FROM patients WHERE is_deleted = 0) AS active_patients,
--     (SELECT COUNT(*) FROM patients WHERE is_deleted = 1) AS deleted_patients,
--     (SELECT COUNT(*) FROM appointments WHERE is_deleted = 0) AS active_appointments,
--     (SELECT COUNT(*) FROM appointments WHERE is_deleted = 1) AS deleted_appointments;
