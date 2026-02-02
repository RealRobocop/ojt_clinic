# FCPC Clinic Management System - Installation Guide

## 🎯 Choose Your Installation Type

### ✅ NEW Installation (Recommended)
Use this if you're installing for the first time.

### 🔄 UPGRADE from Old Version
Use this if you have the old system with single "patients" table.

---

## 🆕 NEW INSTALLATION

### Step 1: Install XAMPP
1. Download from https://www.apachefriends.org/
2. Install to `C:\xampp`
3. Open XAMPP Control Panel
4. Click **Start** for Apache
5. Click **Start** for MySQL
6. Wait for both to show green "Running" status

### Step 2: Create Database
1. Open browser
2. Go to `http://localhost/phpmyadmin`
3. Click **"New"** in left sidebar
4. Database name: `fcpc_clinic`
5. Collation: `utf8mb4_unicode_ci`
6. Click **"Create"**

### Step 3: Import Database Schema
1. Click on `fcpc_clinic` database (left sidebar)
2. Click **"Import"** tab (top menu)
3. Click **"Choose File"**
4. Select **`database.sql`** from extracted folder
5. Scroll down, click **"Go"**
6. Wait for "Import has been successfully finished" message

### Step 4: Add Sample Data (Optional)
1. Stay on Import tab
2. Click **"Choose File"** again
3. Select **`sample_data.sql`**
4. Click **"Go"**
5. Sample data loaded (9 students, 5 employees, test appointments)

### Step 5: Deploy Files
1. Extract the ZIP file
2. Find the `clinic-system` folder
3. Copy the entire folder
4. Paste into: `C:\xampp\htdocs\`
5. Result: `C:\xampp\htdocs\clinic-system\`

### Step 6: Verify Installation
1. Open browser
2. Go to `http://localhost/clinic-system/`
3. You should see the FCPC Clinic dashboard with logo
4. Test adding a student: Patients → Add Patient → Select Student
5. Test adding an employee: Patients → Add Patient → Select Employee

✅ **Installation Complete!**

---

## 🔄 UPGRADE FROM OLD VERSION

### If You See Error: "Table 'fcpc_clinic.patients' doesn't exist"

This means you have the old database structure. Follow these steps:

### Option A: Fresh Installation (Easiest)

1. **Backup Your Data First!**
   ```
   phpMyAdmin → fcpc_clinic → Export → Go
   Save the SQL file
   ```

2. **Drop Old Database**
   ```
   phpMyAdmin → fcpc_clinic → Operations → Drop database
   ```

3. **Follow NEW INSTALLATION steps above**

4. **Import Your Backed Up Data**
   - Use the import feature to bring back your records

### Option B: Migrate Existing Database

1. **Backup First!** (Very Important)
   ```
   phpMyAdmin → fcpc_clinic → Export → Go
   ```

2. **Run Migration Script**
   ```
   phpMyAdmin → fcpc_clinic → Import → Choose migrate_database.sql → Go
   ```

3. **Verify Migration**
   - Check students table exists: `SELECT * FROM students`
   - Check employees table exists: `SELECT * FROM employees`
   - Old patients table backed up as: `patients_backup_old`

4. **Test System**
   - Go to `http://localhost/clinic-system/`
   - Try adding a student
   - Try adding an employee
   - Check appointments work

5. **After Verification (Optional)**
   ```sql
   -- Only do this after everything works!
   DROP TABLE patients_backup_old;
   ```

---

## 🔧 Database Configuration

Default settings (works with XAMPP):
```
Host: localhost
Database: fcpc_clinic
Username: root
Password: (leave empty)
```

**To change settings:**
Edit `includes/db.php`:
```php
$host = 'localhost';
$dbname = 'fcpc_clinic';
$username = 'root';
$password = '';
```

---

## ⚠️ Common Errors & Solutions

### Error: "Can't add employee"
**Cause**: Old database structure
**Solution**: Follow UPGRADE steps above or do fresh installation

### Error: "Table 'fcpc_clinic.patients' doesn't exist"
**Cause**: Old system code with new database or vice versa
**Solution**: 
1. Make sure you have the latest files
2. Run `database.sql` for fresh install OR
3. Run `migrate_database.sql` to upgrade

### Error: "Cannot connect to database"
**Solution**:
1. Check MySQL is running in XAMPP (green status)
2. Verify database name is `fcpc_clinic`
3. Check credentials in `includes/db.php`
4. Try creating database manually in phpMyAdmin

### Error: "Access denied for user 'root'@'localhost'"
**Solution**:
1. In phpMyAdmin, click "User accounts"
2. Find root user for localhost
3. Click "Edit privileges"
4. Ensure all privileges are checked
5. Or set a password and update `includes/db.php`

### Logo Not Showing
**Solution**:
1. Check `assets/logo.png` exists
2. Clear browser cache (Ctrl+F5)
3. Check file permissions
4. Verify path in `index.php`

### Dashboard Blank or Errors
**Solution**:
1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify all AJAX files exist in `/ajax/` folder
4. Check Apache error log: `C:\xampp\apache\logs\error.log`

---

## ✅ Verification Checklist

After installation, verify these work:

- [ ] System loads at `http://localhost/clinic-system/`
- [ ] FCPC logo displays in sidebar
- [ ] Can add a student with education fields
- [ ] Can add an employee with employee type
- [ ] Can create appointment for student
- [ ] Can create appointment for employee
- [ ] Dashboard shows today's appointments
- [ ] Tomorrow's appointments display
- [ ] Statistics charts show in Records tab
- [ ] Filter by education level works
- [ ] Filter by employee type works
- [ ] Can edit student/employee
- [ ] Can delete (with double confirmation)
- [ ] Notification bell shows pending count

---

## 📂 Required Files Structure

```
C:\xampp\htdocs\clinic-system\
├── index.php                    ✅ Main file
├── database.sql                 ✅ Database schema
├── sample_data.sql             ✅ Test data
├── migrate_database.sql        ✅ Upgrade script
├── INSTALL.md                  📖 This file
│
├── includes/
│   └── db.php                  ✅ Must exist
│
├── assets/
│   ├── logo.png                ✅ Must exist
│   ├── styles.css              ✅ Must exist
│   └── app.js                  ✅ Must exist
│
└── ajax/
    ├── patients.php            ✅ Must exist
    ├── appointments.php        ✅ Must exist
    ├── records.php            ✅ Must exist
    ├── import.php             ✅ Must exist
    └── export.php             ✅ Must exist
```

---

## 🆘 Still Having Issues?

1. **Check XAMPP Status**
   - Both Apache and MySQL must be green/running

2. **Check Database**
   ```sql
   -- In phpMyAdmin, run:
   SHOW TABLES;
   -- Should show: students, employees, appointments, appointment_records
   ```

3. **Check File Permissions**
   - Ensure files are not read-only
   - XAMPP user has access

4. **Check PHP Version**
   - PHP 7.4 or higher required
   - Check in XAMPP Control Panel

5. **Clear Everything and Start Fresh**
   - Stop Apache & MySQL
   - Drop database in phpMyAdmin
   - Delete clinic-system folder
   - Start from Step 1

---

## 📞 Quick Help

**Database Error?** → Check MySQL is running + database exists
**Can't Add Employee?** → Run fresh database.sql or migrate_database.sql  
**Logo Missing?** → Check assets/logo.png exists
**Blank Page?** → Check Apache error.log for PHP errors
**Form Not Saving?** → Open browser console (F12) for errors

---

## 🎓 Database Structure

**New System (Separate Tables):**
```
students table      → All student records
employees table     → All employee records
appointments table  → Links via patient_type + patient_id
```

**Old System (Single Table) - DEPRECATED:**
```
patients table → Both students and employees (causes issues)
```

---

## ✨ Post-Installation

After successful installation:

1. **Customize Logo**: Replace `assets/logo.png` with your preferred logo
2. **Add Real Data**: Start adding actual students and employees
3. **Configure Settings**: Adjust system settings as needed
4. **Backup Regularly**: Export database regularly from phpMyAdmin
5. **Train Users**: Show staff how to use the system

---

**Version**: 2.0 (Separate Tables Architecture)
**Last Updated**: January 2024
**For**: First City Providential College

Need more help? Check DEPLOYMENT_GUIDE.md and README.md
