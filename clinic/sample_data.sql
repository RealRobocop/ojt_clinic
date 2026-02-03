_-- First City Providential College - Clinic Management System
-- Sample Data with Separate Tables

USE fcpc_clinic;

-- Sample Student (Different Education Levels)
INSERT INTO Students 
(first_name, last_name, class, age, gender, education_lvl, year_lvl, shs_strand, program, mobile_no, email, address_record) VALUES

-- K-10 Students
('Arron', 'Santos', 'student', 6, 'Male', 'K-10', 'Kinder', NULL, NULL, '09171234567', 'sample@sample.com', '123 Sample St., Angeles City'),
('Sofia', 'Reyes', 'student', 12, 'Female', 'K-10', 'Grade 6', NULL, NULL, '09181234567', 'sample@sample.com', '234 Example Ave., Angeles City'),
('Carlos', 'Garcia', 'student', 14, 'Male', 'K-10', 'Grade 8', NULL, NULL, '09191234567', 'sample@sample.com', '345 Test Blvd., Angeles City'),
('Anna', 'Cruz', 'student', 16, 'Female', 'K-10', 'Grade 10', NULL, NULL, '09201234567', 'sample@sample.com', '456 Demo Rd., Angeles City'),

-- Senior High Students
('Juan', 'Dela Cruz', 'student', 17, 'Male', 'Senior High', 'Grade 11', 'STEM', NULL, '09211234567', 'sample@sample.com', '567 Mock St., Angeles City'),
('Lara', 'Gomez', 'student', 18, 'Female', 'Senior High', 'Grade 12', 'ABM', NULL, '09221234567', 'sample@sample.com', '678 Model St., Angeles City'),

-- College Students
('Maria', 'Mendoza', 'student', 18, 'Female', 'College', '1st Year', NULL, 'BSIT', '09331234567', 'sample@sample.com', '789 Sample St., Angeles City'),
('Karia', 'Mendoza', 'student', 19, 'Female', 'College', '2nd Year', NULL, 'BSBA', '09341234567', 'sample@sample.com', '890 Sample St., Angeles City'),
('Daniel', 'Lopez', 'student', 20, 'Male', 'College', '3rd Year', NULL, 'BSCRIM', '09351234567', 'sample@sample.com', '901 Sample St., Angeles City'),
('Rhea', 'Torres', 'student', 21, 'Female', 'College', '4th Year', NULL, 'BSED', '09361234567', 'sample@sample.com', '012 Sample St., Angeles City');

INSERT INTO Employees 
(first_name, last_name, age, gender, mobile_no, email, class, department, address_record) VALUES

('Pedro', 'Aquino', 35, 'Male', '09261234567', 'pedro.aquino@fcpc.edu.ph','Employee', 'Registrar', '555 Macapagal Ave., Angeles City'),
('Carmen', 'Santos', 42, 'Female', '09271234567', 'carmen.santos@fcpc.edu.ph','Employee', 'Registrar', '666 Osmeña St., Angeles City'),
('Luis', 'Fernandez', 38, 'Male', '09281234567', 'luis.fernandez@fcpc.edu.ph', 'Employee', 'Registrar', '777 Quirino Ave., Angeles City'),
('Gloria', 'Martinez', 45, 'Female', '09291234567', 'gloria.martinez@fcpc.edu.ph', 'Employee', 'Registrar', '888 Laurel Blvd., Angeles City'),
('Antonio', 'Lopez', 52, 'Male', '09301234567', 'antonio.lopez@fcpc.edu.ph', 'Employee', 'Registrar', '999 Magsaysay Dr., Angeles City'),
('Marvin', 'Reyes', 29, 'Male', '09311234567', 'marvin.reyes@fcpc.edu.ph', 'Employee', 'ICT', '101 Tech Park Rd., Angeles City'),
('Helen', 'Cruz', 47, 'Female', '09321234567', 'helen.cruz@fcpc.edu.ph', 'Employee', 'Registrar', '202 Records Ave., Angeles City'),
('Ramon', 'Dizon', 33, 'Male', '09331234567', 'ramon.dizon@fcpc.edu.ph', 'Employee', 'Registrar', '303 Maintenance Rd., Angeles City');

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
