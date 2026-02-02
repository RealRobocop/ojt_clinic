# Fix Records Not Showing Issue

## Problem
Appointments aren't appearing in the Records tab even though they're marked as completed in the database.

## Root Cause
The database trigger that automatically archives completed appointments to the `appointment_records` table is either:
1. Not installed in the database
2. Not firing properly due to permissions
3. Missing due to incomplete database import

---

## Solution 1: Use the Archive Button (Easiest)

### Steps:
1. Go to **Records** tab in the system
2. Click the **"Archive Completed"** button (top right)
3. Confirm the action
4. All completed appointments will be manually archived
5. Reload the page - records should now appear!

### What This Does:
- Finds all appointments with status = 'completed'
- Copies their data to `appointment_records` table
- Includes full patient information (name, type, education level, etc.)

---

## Solution 2: Fix the Database Trigger

### Steps:
1. Open **phpMyAdmin**
2. Select `fcpc_clinic` database
3. Click **"SQL"** tab
4. Click **"Import file"** (or copy-paste from `fix_trigger.sql`)
5. Run the script
6. You should see "Trigger created successfully"

### What This Does:
- Drops the old trigger
- Creates a new trigger
- Shows statistics on completed vs archived appointments
- Lists appointments that need archiving

### Verify Trigger:
```sql
SHOW TRIGGERS;
```
You should see `after_appointment_complete` in the list.

---

## Solution 3: Manual SQL Archive

If you want to archive immediately using SQL:

### For Student Appointments:
```sql
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
    a.appointment_date,
    a.appointment_time,
    a.appointment_type,
    a.status,
    a.notes,
    YEAR(a.appointment_date)
FROM appointments a
JOIN students s ON a.patient_id = s.id
WHERE a.patient_type = 'Student' 
  AND a.status = 'completed' 
  AND a.is_deleted = 0
  AND NOT EXISTS (
      SELECT 1 FROM appointment_records ar 
      WHERE ar.patient_id = a.patient_id 
      AND ar.appointment_date = a.appointment_date 
      AND ar.appointment_time = a.appointment_time
  );
```

### For Employee Appointments:
```sql
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
    a.appointment_date,
    a.appointment_time,
    a.appointment_type,
    a.status,
    a.notes,
    YEAR(a.appointment_date)
FROM appointments a
JOIN employees e ON a.patient_id = e.id
WHERE a.patient_type = 'Employee' 
  AND a.status = 'completed' 
  AND a.is_deleted = 0
  AND NOT EXISTS (
      SELECT 1 FROM appointment_records ar 
      WHERE ar.patient_id = a.patient_id 
      AND ar.appointment_date = a.appointment_date 
      AND ar.appointment_time = a.appointment_time
  );
```

---

## Diagnostic Queries

### Check Completed Appointments:
```sql
SELECT COUNT(*) as total_completed
FROM appointments
WHERE status = 'completed' AND is_deleted = 0;
```

### Check Archived Records:
```sql
SELECT COUNT(*) as total_archived
FROM appointment_records;
```

### Find Unarchived Appointments:
```sql
SELECT 
    a.id,
    a.patient_type,
    CASE 
        WHEN a.patient_type = 'Student' THEN CONCAT(s.first_name, ' ', s.last_name)
        WHEN a.patient_type = 'Employee' THEN CONCAT(e.first_name, ' ', e.last_name)
    END as patient_name,
    a.appointment_date,
    a.status
FROM appointments a
LEFT JOIN students s ON a.patient_id = s.id AND a.patient_type = 'Student'
LEFT JOIN employees e ON a.patient_id = e.id AND a.patient_type = 'Employee'
WHERE a.status = 'completed' 
AND a.is_deleted = 0
AND NOT EXISTS (
    SELECT 1 FROM appointment_records ar 
    WHERE ar.patient_id = a.patient_id 
    AND ar.appointment_date = a.appointment_date 
    AND ar.appointment_time = a.appointment_time
);
```

---

## Why Records Need Manual Archiving

### Trigger Should Auto-Archive When:
1. Appointment status changes from anything → 'completed'
2. This happens in the Appointments tab
3. Trigger fires automatically
4. Record added to `appointment_records` table

### Trigger Might Not Work If:
- MySQL user doesn't have TRIGGER privilege
- Trigger wasn't imported with database.sql
- Database was created before triggers were added
- MySQL version doesn't support triggers (rare)

---

## Permanent Fix

### Option A: Always Use Archive Button
- After marking appointments as completed
- Click "Archive Completed" button in Records tab
- Done!

### Option B: Ensure Trigger Works
1. Run `fix_trigger.sql` once
2. Test by:
   - Creating new appointment
   - Marking it as completed
   - Check if it appears in Records tab immediately
3. If yes, trigger is working!
4. If no, use Archive button

---

## Statistics Not Showing

If statistics charts are blank:

### Cause:
No data in `appointment_records` table

### Solution:
1. Archive completed appointments (using any method above)
2. Go to Records → Statistics tab
3. Charts should populate with data

### Note:
Statistics ONLY use data from `appointment_records` table, NOT from `appointments` table. This is by design for historical analysis.

---

## Quick Fix Checklist

- [ ] Import `database.sql` completely
- [ ] OR run `fix_trigger.sql` 
- [ ] Mark some appointments as 'completed'
- [ ] Click "Archive Completed" button
- [ ] Verify records appear in Records tab
- [ ] Check Statistics tab has charts

---

## Files Included

| File | Purpose |
|------|---------|
| `fix_trigger.sql` | Recreates the auto-archive trigger |
| `ajax/archive.php` | Handles manual archiving |
| This document | Troubleshooting guide |

---

**Recommendation:** Use the **"Archive Completed"** button - it's the most reliable method and works regardless of trigger status! 🎯
