# FCPC Clinic Management System - Deployment Guide

## 🚀 Quick Deployment (5 Minutes)

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP to `C:\xampp` (default location)
3. Start **Apache** and **MySQL** from XAMPP Control Panel

### Step 2: Setup Database
1. Open browser and navigate to: `http://localhost/phpmyadmin`
2. Click **"Import"** tab at the top
3. Click **"Choose File"** button
4. Select `database.sql` from the extracted folder
5. Click **"Go"** button at the bottom
6. Wait for success message (database `fcpc_clinic` created)

### Step 3: Add Sample Data (Optional but Recommended)
1. Stay in phpMyAdmin
2. Click **"Import"** tab again
3. Select `sample_data.sql`
4. Click **"Go"**
5. Sample data now loaded (9 students, 5 employees, test appointments)

### Step 4: Deploy Files
1. Extract the `clinic-system` folder from the ZIP
2. Copy the entire `clinic-system` folder
3. Paste it into: `C:\xampp\htdocs\`
4. Final path should be: `C:\xampp\htdocs\clinic-system\`

### Step 5: Access System
1. Open your web browser
2. Navigate to: `http://localhost/clinic-system/`
3. System is ready to use! 🎉

## 📁 File Structure After Deployment

```
C:\xampp\htdocs\clinic-system\
├── index.php                    # Main application
├── database.sql                 # Database schema
├── sample_data.sql              # Test data
├── DEPLOYMENT_GUIDE.md          # This file
├── README.md                    # Full documentation
├── QUICKSTART.md                # Quick start guide
│
├── includes/
│   └── db.php                   # Database configuration
│
├── assets/
│   ├── logo.png                 # FCPC logo
│   ├── styles.css               # All styling
│   └── app.js                   # JavaScript
│
└── ajax/
    ├── patients.php             # Patient operations (students & employees)
    ├── appointments.php         # Appointment management
    ├── records.php              # Historical records & statistics
    ├── import.php               # Data import handler
    └── export.php               # Data export handler
```

## 🔧 Database Configuration

The system is pre-configured for XAMPP default settings:
- **Host**: localhost
- **Database**: fcpc_clinic
- **Username**: root
- **Password**: (empty)

If you need different settings, edit `includes/db.php`:

```php
$host = 'localhost';
$dbname = 'fcpc_clinic';
$username = 'root';
$password = '';
```

## 🎯 Key Features Implemented

### 1. **Separate Tables for Students & Employees**
- Students stored in `students` table
- Employees stored in `employees` table
- Both visible in single unified Patients tab
- Prevents form submission issues

### 2. **Enhanced Patient Management**
**Students have:**
- Education Level (Elementary, Junior High, Senior High, College)
- Grade Level (Grade 1-12, 1st-5th Year)
- Course/Track (Philippine education system)

**Employees have:**
- Employee Type (Teacher, Staff, Administration)
- Department

### 3. **Dashboard Features**
- Today's appointments tracker (excludes completed)
- Tomorrow's appointments preview
- Real-time statistics
- Sort and filter options

### 4. **Advanced Filtering**
- Patient tab: Filter by type, education level, employee type
- Appointments: Filter by year, patient type, status
- Records: Statistics with charts

### 5. **FCPC Logo Integration**
- Official First City Providential College logo displayed
- Visible in sidebar header

## 🧪 Testing the System

After deployment, you can:

1. **View Dashboard**
   - See today's appointments
   - Check tomorrow's schedule

2. **Add a Student**
   - Click Patients → Add Patient
   - Select "Student"
   - Choose education level
   - Fill in all required fields
   - Save successfully

3. **Add an Employee**
   - Click Patients → Add Patient
   - Select "Employee"
   - Choose employee type
   - Enter department
   - Save successfully

4. **Create Appointment**
   - Select patient from dropdown (shows type)
   - Choose date and time
   - Save appointment

5. **View Statistics**
   - Go to Records tab
   - Click Statistics sub-tab
   - See charts for gender, education levels, employee types

## ⚠️ Troubleshooting

### Problem: "Can't add employee" error
**Solution**: This is fixed! System now uses separate tables for students and employees.

### Problem: Database connection error
**Solution**: 
- Verify MySQL is running in XAMPP
- Check database name is `fcpc_clinic`
- Verify credentials in `includes/db.php`

### Problem: Logo not showing
**Solution**:
- Ensure `assets/logo.png` exists
- Check file permissions
- Clear browser cache

### Problem: Blank page or errors
**Solution**:
- Check Apache error log: `C:\xampp\apache\logs\error.log`
- Enable PHP error display in `php.ini`
- Verify all files copied correctly

### Problem: Import/Export not working
**Solution**:
- Check PHP upload limit in `php.ini`
- Verify file permissions on uploads folder
- Use supported formats (Excel, CSV, SQL)

## 🔐 Security Notes

- No login required (as specified)
- Double delete confirmation with college name
- POST-only data modifications
- SQL injection protection via PDO
- XSS prevention via HTML escaping

## 📊 Database Tables

### Students Table
- Stores all student information
- Education levels and courses
- Linked to appointments via patient_type='Student'

### Employees Table
- Stores all employee information
- Employee types and departments
- Linked to appointments via patient_type='Employee'

### Appointments Table
- Has patient_type field (Student/Employee)
- Has patient_id pointing to respective table
- Auto-archives when status='completed'

### Appointment_records Table
- 20-year retention
- Stores complete patient info at time of visit
- Used for statistics generation

## 🎨 UI Components

- **Navy & Gold Theme**: FCPC official colors
- **Responsive Design**: Works on desktop, tablet, mobile
- **Tab System**: Records split into Past Records and Statistics
- **Charts**: Gender, education levels, employee types, top courses
- **Real-time**: Dashboard auto-refreshes every 30 seconds

## 📝 Common Tasks

### Add New Course/Track
Edit `assets/app.js`, find `educationData` object:
```javascript
College: {
    courses: [
        'BS Information Technology (BSIT)',
        // Add your new course here
    ]
}
```

### Change System Colors
Edit `assets/styles.css`, update CSS variables:
```css
:root {
    --color-navy-dark: #1006ba;
    --color-gold-main: #d4af37;
    /* Modify these values */
}
```

### Adjust Record Retention Period
Edit database or system settings:
```sql
UPDATE system_settings 
SET setting_value = '30' 
WHERE setting_key = 'record_retention_years';
```

## 🆘 Support

For issues:
1. Check this deployment guide
2. Review README.md for detailed documentation
3. Check QUICKSTART.md for basic usage
4. Inspect browser console for JavaScript errors
5. Check Apache/PHP error logs

## ✅ Deployment Checklist

- [ ] XAMPP installed and running
- [ ] Database created (database.sql imported)
- [ ] Sample data loaded (optional)
- [ ] Files copied to htdocs
- [ ] Can access http://localhost/clinic-system/
- [ ] Logo displays correctly
- [ ] Can add students
- [ ] Can add employees  
- [ ] Can create appointments
- [ ] Statistics charts show data
- [ ] Dashboard shows today's appointments

---

**System Version**: 2.0 (Separate Tables)
**Last Updated**: January 2024
**Built for**: First City Providential College

🏥 **Ready to manage your clinic efficiently!**
