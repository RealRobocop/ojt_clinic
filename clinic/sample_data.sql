-- First City Providential College - Clinic Management System
-- Sample Data with Separate Tables

USE fcpc_clinic;

-- Sample Students (Different Education Levels)
INSERT INTO students (first_name, last_name, age, gender, phone, email, student_id, education_level, grade_level, course_track, address) VALUES
-- Elementary
('Miguel', 'Santos', 8, 'Male', '09171234567', 'miguel.santos@fcpc.edu.ph', 'ELEM-2024-001', 'Elementary', 'Grade 3', 'General Education', '123 Magsaysay St., Angeles City'),
('Sofia', 'Reyes', 11, 'Female', '09181234567', 'sofia.reyes@fcpc.edu.ph', 'ELEM-2024-015', 'Elementary', 'Grade 6', 'General Education', '456 Rizal Ave., Angeles City'),

-- Junior High
('Carlos', 'Garcia', 13, 'Male', '09191234567', 'carlos.garcia@fcpc.edu.ph', 'JHS-2024-002', 'Junior High', 'Grade 8', 'General Education', '789 Mabini St., Angeles City'),
('Anna', 'Cruz', 15, 'Female', '09201234567', 'anna.cruz@fcpc.edu.ph', 'JHS-2023-030', 'Junior High', 'Grade 10', 'General Education', '321 Bonifacio Rd., Angeles City'),

-- Senior High
('Juan', 'Dela Cruz', 17, 'Male', '09211234567', 'juan.delacruz@fcpc.edu.ph', 'SHS-2024-003', 'Senior High', 'Grade 11', 'STEM (Science, Technology, Engineering, Mathematics)', '654 Luna St., Angeles City'),
('Maria', 'Mendoza', 18, 'Female', '09221234567', 'maria.mendoza@fcpc.edu.ph', 'SHS-2023-012', 'Senior High', 'Grade 12', 'ABM (Accountancy, Business, Management)', '111 Del Pilar St., Angeles City'),

-- College
('Roberto', 'Torres', 20, 'Male', '09231234567', 'roberto.torres@fcpc.edu.ph', 'COL-2024-001', 'College', '2nd Year', 'BS Information Technology (BSIT)', '222 Aguinaldo Ave., Angeles City'),
('Elena', 'Ramos', 21, 'Female', '09241234567', 'elena.ramos@fcpc.edu.ph', 'COL-2023-015', 'College', '3rd Year', 'BS Business Administration (BSBA)', '333 Quezon Blvd., Angeles City'),
('Diego', 'Villanueva', 19, 'Male', '09251234567', 'diego.villanueva@fcpc.edu.ph', 'COL-2024-020', 'College', '1st Year', 'BS Nursing (BSN)', '444 Roxas St., Angeles City');

-- Sample Employees (Different Types)
INSERT INTO employees (first_name, last_name, age, gender, phone, email, employee_id, employee_type, department, address) VALUES
('Pedro', 'Aquino', 35, 'Male', '09261234567', 'pedro.aquino@fcpc.edu.ph', 'TEACH-2018-001', 'Teacher', 'Information Technology', '555 Macapagal Ave., Angeles City'),
('Carmen', 'Santos', 42, 'Female', '09271234567', 'carmen.santos@fcpc.edu.ph', 'TEACH-2015-008', 'Teacher', 'Business Administration', '666 Osmeña St., Angeles City'),
('Luis', 'Fernandez', 38, 'Male', '09281234567', 'luis.fernandez@fcpc.edu.ph', 'STAFF-2020-015', 'Staff', 'Library Services', '777 Quirino Ave., Angeles City'),
('Gloria', 'Martinez', 45, 'Female', '09291234567', 'gloria.martinez@fcpc.edu.ph', 'ADMIN-2012-003', 'Administration', 'Human Resources', '888 Laurel Blvd., Angeles City'),
('Antonio', 'Lopez', 52, 'Male', '09301234567', 'antonio.lopez@fcpc.edu.ph', 'STAFF-2010-005', 'Staff', 'Facilities Management', '999 Magsaysay Dr., Angeles City');

-- Sample Appointments for Today (using separate tables)
INSERT INTO appointments (patient_type, patient_id, appointment_date, appointment_time, appointment_type, status, notes) VALUES
('Student', 1, CURDATE(), '08:30:00', 'Regular Checkup', 'confirmed', 'Elementary student - annual checkup'),
('Student', 3, CURDATE(), '09:00:00', 'Consultation', 'pending', 'Junior high student - fever symptoms'),
('Student', 5, CURDATE(), '09:30:00', 'Medical Certificate', 'confirmed', 'Senior high - for sports participation'),
('Student', 7, CURDATE(), '10:00:00', 'Follow-up Visit', 'checked-in', 'College student - follow-up on treatment'),
('Employee', 1, CURDATE(), '10:30:00', 'Vaccination', 'pending', 'Teacher - annual flu shot'),
('Employee', 3, CURDATE(), '11:00:00', 'Regular Checkup', 'confirmed', 'Staff - pre-employment medical'),
('Student', 8, CURDATE(), '14:00:00', 'Consultation', 'pending', 'College student - headache complaint');

-- Sample Appointments for Tomorrow
INSERT INTO appointments (patient_type, patient_id, appointment_date, appointment_time, appointment_type, status, notes) VALUES
('Student', 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Regular Checkup', 'confirmed', 'Elementary - routine checkup'),
('Student', 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:30:00', 'Consultation', 'pending', 'Junior high - cough and cold'),
('Student', 6, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'Medical Certificate', 'confirmed', 'Senior high - certificate needed'),
('Student', 9, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 'Follow-up Visit', 'pending', 'College - lab result review'),
('Employee', 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', 'Regular Checkup', 'confirmed', 'Teacher - annual physical exam'),
('Employee', 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', 'Vaccination', 'pending', 'Administration - booster shot');

-- Sample Past Appointments (for records)
INSERT INTO appointments (patient_type, patient_id, appointment_date, appointment_time, appointment_type, status, notes, created_at) VALUES
('Student', 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '09:00:00', 'Regular Checkup', 'completed', 'Elementary checkup - all normal', DATE_SUB(NOW(), INTERVAL 7 DAY)),
('Student', 3, DATE_SUB(CURDATE(), INTERVAL 14 DAY), '10:00:00', 'Consultation', 'completed', 'Treated for minor ailment', DATE_SUB(NOW(), INTERVAL 14 DAY)),
('Student', 5, DATE_SUB(CURDATE(), INTERVAL 21 DAY), '14:00:00', 'Vaccination', 'completed', 'Vaccine administered successfully', DATE_SUB(NOW(), INTERVAL 21 DAY)),
('Student', 7, DATE_SUB(CURDATE(), INTERVAL 30 DAY), '09:30:00', 'Medical Certificate', 'completed', 'Certificate issued', DATE_SUB(NOW(), INTERVAL 30 DAY)),
('Employee', 1, DATE_SUB(CURDATE(), INTERVAL 45 DAY), '11:00:00', 'Follow-up Visit', 'completed', 'Recovery progressing well', DATE_SUB(NOW(), INTERVAL 45 DAY));

-- Sample Historical Records (manually added for demonstration)
INSERT INTO appointment_records (patient_type, patient_id, patient_name, patient_gender, education_level, grade_level, course_track, employee_type, department, appointment_date, appointment_time, appointment_type, status, notes, record_year) VALUES
-- Student records
('Student', 1, 'Miguel Santos', 'Male', 'Elementary', 'Grade 3', 'General Education', NULL, NULL, '2023-12-15', '09:00:00', 'Regular Checkup', 'completed', 'Annual checkup - all normal', 2023),
('Student', 3, 'Carlos Garcia', 'Male', 'Junior High', 'Grade 8', 'General Education', NULL, NULL, '2023-11-20', '10:00:00', 'Consultation', 'completed', 'Cold symptoms treated', 2023),
('Student', 5, 'Juan Dela Cruz', 'Male', 'Senior High', 'Grade 11', 'STEM (Science, Technology, Engineering, Mathematics)', NULL, NULL, '2023-10-10', '14:00:00', 'Medical Certificate', 'completed', 'Sports clearance issued', 2023),
('Student', 7, 'Roberto Torres', 'Male', 'College', '2nd Year', 'BS Information Technology (BSIT)', NULL, NULL, '2023-09-05', '09:30:00', 'Regular Checkup', 'completed', 'Annual physical exam', 2023),
('Student', 8, 'Elena Ramos', 'Female', 'College', '3rd Year', 'BS Business Administration (BSBA)', NULL, NULL, '2023-08-15', '11:00:00', 'Vaccination', 'completed', 'Flu vaccination given', 2023),

-- Employee records
('Employee', 1, 'Pedro Aquino', 'Male', NULL, NULL, NULL, 'Teacher', 'Information Technology', '2023-07-20', '09:00:00', 'Regular Checkup', 'completed', 'Annual medical exam', 2023),
('Employee', 3, 'Luis Fernandez', 'Male', NULL, NULL, NULL, 'Staff', 'Library Services', '2023-06-15', '10:00:00', 'Consultation', 'completed', 'Work-related injury treated', 2023),
('Employee', 4, 'Gloria Martinez', 'Female', NULL, NULL, NULL, 'Administration', 'Human Resources', '2023-05-10', '14:00:00', 'Medical Certificate', 'completed', 'Fitness to work certificate', 2023);

-- Sample Records from 2022
INSERT INTO appointment_records (patient_type, patient_id, patient_name, patient_gender, education_level, grade_level, course_track, employee_type, department, appointment_date, appointment_time, appointment_type, status, notes, record_year) VALUES
('Student', 2, 'Sofia Reyes', 'Female', 'Elementary', 'Grade 6', 'General Education', NULL, NULL, '2022-12-10', '09:00:00', 'Regular Checkup', 'completed', 'End of year checkup', 2022),
('Student', 4, 'Anna Cruz', 'Female', 'Junior High', 'Grade 10', 'General Education', NULL, NULL, '2022-11-15', '10:00:00', 'Consultation', 'completed', 'Allergy consultation', 2022),
('Employee', 2, 'Carmen Santos', 'Female', NULL, NULL, NULL, 'Teacher', 'Business Administration', '2022-10-05', '11:00:00', 'Vaccination', 'completed', 'Annual flu shot', 2022);

-- Sample Records from 2021
INSERT INTO appointment_records (patient_type, patient_id, patient_name, patient_gender, education_level, grade_level, course_track, employee_type, department, appointment_date, appointment_time, appointment_type, status, notes, record_year) VALUES
('Student', 6, 'Maria Mendoza', 'Female', 'Senior High', 'Grade 12', 'ABM (Accountancy, Business, Management)', NULL, NULL, '2021-12-20', '09:00:00', 'Medical Certificate', 'completed', 'Graduation clearance', 2021),
('Student', 9, 'Diego Villanueva', 'Male', 'College', '1st Year', 'BS Nursing (BSN)', NULL, NULL, '2021-09-15', '10:00:00', 'Regular Checkup', 'completed', 'Pre-enrollment medical', 2021),
('Employee', 5, 'Antonio Lopez', 'Male', NULL, NULL, NULL, 'Staff', 'Facilities Management', '2021-08-10', '11:00:00', 'Regular Checkup', 'completed', 'Maintenance staff checkup', 2021);
