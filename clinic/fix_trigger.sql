-- Fix Trigger Script
-- Run this if appointments aren't being automatically archived

USE fcpc_clinic;

-- Drop the old trigger if it exists
DROP TRIGGER IF EXISTS after_appointment_complete;

-- Recreate the trigger
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

-- Verify trigger was created
SHOW TRIGGERS LIKE 'appointments';

-- Check how many completed appointments exist
SELECT COUNT(*) as completed_appointments 
FROM appointments 
WHERE status = 'completed' AND is_deleted = 0;

-- Check how many records are in archive
SELECT COUNT(*) as archived_records 
FROM appointment_records;

-- Show completed appointments that aren't archived yet
SELECT a.id, a.patient_type, a.patient_id, a.appointment_date, a.appointment_time
FROM appointments a
WHERE a.status = 'completed' 
AND a.is_deleted = 0
AND NOT EXISTS (
    SELECT 1 FROM appointment_records ar 
    WHERE ar.patient_id = a.patient_id 
    AND ar.appointment_date = a.appointment_date 
    AND ar.appointment_time = a.appointment_time
);
