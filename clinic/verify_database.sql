-- Database Verification Script
-- Run this in phpMyAdmin to verify your database structure is correct

USE fcpc_clinic;

-- Check if NEW tables exist
SELECT 'Checking students table...' as Status;
SELECT COUNT(*) as students_count FROM students;

SELECT 'Checking employees table...' as Status;
SELECT COUNT(*) as employees_count FROM employees;

SELECT 'Checking appointments table structure...' as Status;
SHOW COLUMNS FROM appointments LIKE 'patient_type';

-- Check if OLD table exists (should NOT exist)
SELECT 'Checking for old patients table (should show error if correct)...' as Status;
-- This should fail if database is correct
-- SELECT COUNT(*) FROM patients;

-- Show all tables
SELECT 'All tables in database:' as Status;
SHOW TABLES;

-- Count records
SELECT 'Record counts:' as Status;
SELECT 
    (SELECT COUNT(*) FROM students WHERE is_deleted = 0) as total_students,
    (SELECT COUNT(*) FROM employees WHERE is_deleted = 0) as total_employees,
    (SELECT COUNT(*) FROM appointments WHERE is_deleted = 0) as total_appointments,
    (SELECT COUNT(*) FROM appointment_records) as total_records;

-- Check trigger exists
SELECT 'Checking trigger:' as Status;
SHOW TRIGGERS LIKE 'appointments';

SELECT '=== VERIFICATION COMPLETE ===' as Status;
SELECT 'If you see students and employees tables with data, everything is correct!' as Message;
