# FCPC Clinic System - Troubleshooting Guide

## ⚠️ ERROR: "Table 'fcpc_clinic.patients' doesn't exist"

This is the most common error and means your database still has the OLD structure or files are using OLD code.

### ✅ SOLUTION 1: Complete Fresh Installation (Recommended)

**Step 1: Backup First (If you have data)**
```
1. Open phpMyAdmin
2. Click on fcpc_clinic database
3. Click "Export" tab
4. Click "Go" button
5. Save the .sql file
```

**Step 2: Delete Old Database**
```
1. In phpMyAdmin, click fcpc_clinic
2. Click "Operations" tab
3. Scroll down to "Remove database"
4. Click "Drop the database (DROP)"
5. Confirm deletion
```

**Step 3: Create Fresh Database**
```
1. Click "New" in phpMyAdmin sidebar
2. Database name: fcpc_clinic
3. Collation: utf8mb4_unicode_ci
4. Click "Create"
```

**Step 4: Import New Schema**
```
1. Click on fcpc_clinic database
2. Click "Import" tab
3. Choose file: database.sql
4. Click "Go"
5. Wait for success message
```

**Step 5: Import Sample Data (Optional)**
```
1. Click "Import" tab again
2. Choose file: sample_data.sql
3. Click "Go"
```

**Step 6: Verify Structure**
```
1. Click "Structure" tab
2. You should see these tables:
   ✅ students
   ✅ employees
   ✅ appointments
   ✅ appointment_records
   ✅ medical_records
   ✅ system_settings
   ❌ NO "patients" table
```

**Step 7: Test System**
```
1. Go to http://localhost/clinic-system/
2. Try adding a student
3. Try adding an employee
4. Both should work!
```

---

### ✅ SOLUTION 2: Use Migration Script (If you want to keep data)

**Step 1: Backup Your Database**
```
phpMyAdmin → fcpc_clinic → Export → Go
Save the file!
```

**Step 2: Run Migration Script**
```
1. phpMyAdmin → fcpc_clinic
2. Click "Import"
3. Choose: migrate_database.sql
4. Click "Go"
5. Wait for completion
```

**Step 3: Verify Migration**
```
1. Click "Structure" tab
2. Check you have:
   ✅ students (with data)
   ✅ employees (with data)
   ✅ appointments (with patient_type column)
   ✅ patients_backup_old (your old data, safe)
```

**Step 4: Test System**
```
1. Go to http://localhost/clinic-system/
2. You should see all your old data
3. Try adding new student - should work
4. Try adding new employee - should work
```

---

## 🔍 Verification Checklist

Run this query in phpMyAdmin SQL tab:

```sql
USE fcpc_clinic;

-- Should return data:
SELECT COUNT(*) as student_count FROM students;
SELECT COUNT(*) as employee_count FROM employees;

-- Should show patient_type column:
DESCRIBE appointments;

-- Should NOT exist (will show error):
SELECT * FROM patients;
```

Or run the included `verify_database.sql` script.

---

## 🐛 Other Common Errors

### Error: "Can't add employee" / "Form not submitting"

**Cause**: Old database structure or files mismatch

**Solution**:
1. Make sure you have the latest files (v2.0)
2. Run `database.sql` for fresh install
3. Clear browser cache (Ctrl+Shift+Delete)
4. Try again

---

### Error: "Unknown column 'patient_type' in appointments"

**Cause**: Old database schema

**Solution**:
```sql
-- Run this in phpMyAdmin:
ALTER TABLE appointments 
ADD COLUMN patient_type ENUM('Student', 'Employee') NOT NULL DEFAULT 'Student' AFTER id;
```

Or run complete `migrate_database.sql`

---

### Error: "Call to undefined function..."

**Cause**: PHP version too old

**Solution**:
1. Check PHP version in XAMPP Control Panel
2. Must be PHP 7.4 or higher
3. Update XAMPP if needed

---

### Error: Database connection failed

**Cause**: MySQL not running or wrong credentials

**Solution**:
1. Check MySQL is green/running in XAMPP
2. Verify database exists in phpMyAdmin
3. Check `includes/db.php`:
   ```php
   $host = 'localhost';
   $dbname = 'fcpc_clinic';
   $username = 'root';
   $password = ''; // Usually empty for XAMPP
   ```

---

### Logo not showing

**Solutions**:
1. Check `assets/logo.png` exists
2. Clear browser cache (Ctrl+F5)
3. Check browser console (F12) for 404 errors
4. Verify file permissions

---

### Dashboard shows no data

**Cause**: No appointments for today

**Solution**:
1. Add test appointments with today's date
2. Or import `sample_data.sql`
3. Check if MySQL date/time is correct

---

### Statistics charts not showing

**Cause**: No historical data or Chart.js not loading

**Solution**:
1. Check browser console (F12) for errors
2. Verify internet connection (Chart.js from CDN)
3. Add some completed appointments
4. Wait for records to archive

---

### Import/Export not working

**Solutions**:
1. Check file size limits in php.ini:
   ```
   upload_max_filesize = 50M
   post_max_size = 50M
   ```
2. Restart Apache after changing php.ini
3. Use supported formats: .xlsx, .xls, .csv, .sql

---

## 📋 Quick Diagnostic Commands

Run these in phpMyAdmin SQL tab to diagnose issues:

```sql
-- Check database exists
SHOW DATABASES LIKE 'fcpc_clinic';

-- Check tables exist
USE fcpc_clinic;
SHOW TABLES;

-- Check students table structure
DESCRIBE students;

-- Check employees table structure
DESCRIBE employees;

-- Check appointments has patient_type
SHOW COLUMNS FROM appointments LIKE 'patient_type';

-- Count records
SELECT 
    (SELECT COUNT(*) FROM students) as students,
    (SELECT COUNT(*) FROM employees) as employees,
    (SELECT COUNT(*) FROM appointments) as appointments;

-- Check if old patients table exists (should error)
SELECT COUNT(*) FROM patients;
```

---

## 🔄 Complete System Reset (Last Resort)

If nothing works, do a complete reset:

**1. Stop XAMPP**
```
Stop Apache
Stop MySQL
```

**2. Backup Data**
```
Export database from phpMyAdmin
Copy clinic-system folder
```

**3. Delete Everything**
```
phpMyAdmin → Drop fcpc_clinic database
Delete C:\xampp\htdocs\clinic-system folder
```

**4. Fresh Start**
```
Start MySQL
Create new fcpc_clinic database
Import database.sql
Import sample_data.sql
Copy fresh clinic-system folder to htdocs
```

**5. Test**
```
Go to http://localhost/clinic-system/
Add a student
Add an employee
```

---

## 📞 Still Need Help?

### Check These Files Match:

**database.sql should have:**
- CREATE TABLE students
- CREATE TABLE employees
- NO CREATE TABLE patients

**ajax/patients.php should have:**
- Queries to students table
- Queries to employees table
- NO queries to patients table

**index.php should have:**
```php
$studentCount = $pdo->query("SELECT COUNT(*) FROM students...
$employeeCount = $pdo->query("SELECT COUNT(*) FROM employees...
```

NOT:
```php
$pdo->query("SELECT COUNT(*) FROM patients...
```

### File Checksums:

Run `verify_database.sql` to check your database structure.

### Get Latest Files:

Make sure you have version 2.0 with separate tables architecture.

---

## ✅ Success Indicators

You'll know it's working when:

- ✅ Can add students without errors
- ✅ Can add employees without errors
- ✅ Dashboard shows today's appointments
- ✅ No "table patients doesn't exist" errors
- ✅ Logo displays correctly
- ✅ All filters work properly

---

## 🎓 Understanding the New Structure

**Old System (v1.0) - BROKEN:**
```
patients table (both students AND employees mixed)
↓
Caused form submission issues
```

**New System (v2.0) - WORKING:**
```
students table (only students)
employees table (only employees)
↓
appointments.patient_type → links to correct table
↓
Works perfectly!
```

---

**Version**: 2.0
**Last Updated**: January 2024
**Critical Fix**: Separate tables for students and employees
