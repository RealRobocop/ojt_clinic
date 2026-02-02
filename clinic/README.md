# First City Providential College - Clinic Management System

A comprehensive clinic management system designed specifically for First City Providential College with advanced features for patient tracking, appointment management, and historical record keeping.

## Features

### Core Functionality
- **Real-time Dashboard**: Live tracking of today's appointments with sort/filter capabilities
- **Patient Management**: Complete CRUD operations for both students and employees
- **Appointment System**: Full appointment scheduling with status tracking
- **Historical Records**: 20-year retention policy with automatic archiving
- **Import/Export**: Support for Excel, CSV, and SQL file formats

### Advanced Features
- **Smart Filtering**: Filter patients by type (Student/Employee) and search by name
- **Appointment Filters**: Filter by year, patient type, and status
- **Real-time Notifications**: Badge showing unconfirmed appointments count
- **Secure Deletion**: Double confirmation with college name verification
- **Auto-archiving**: Completed appointments automatically stored in historical records
- **Dashboard Analytics**: Live statistics and today's appointment tracker

### Security Features
- **Double Confirmation Delete**: Users must type "First City Providential College" to delete
- **Soft Delete**: Data is marked as deleted but retained in database
- **POST-only Operations**: All data modifications use POST requests (no GET)

## System Requirements

- **Web Server**: Apache 2.4+ (XAMPP recommended)
- **PHP**: 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Browser**: Modern browser (Chrome, Firefox, Edge, Safari)

## Installation Instructions

### 1. Setup XAMPP
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel

### 2. Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "Import" tab
3. Choose `database.sql` from the project folder
4. Click "Go" to execute the SQL script
5. Database `fcpc_clinic` will be created with all tables

### 3. File Deployment
1. Copy the entire `clinic-system` folder to `C:\xampp\htdocs\`
2. Ensure the folder structure looks like:
   ```
   C:\xampp\htdocs\clinic-system\
   ├── index.php
   ├── database.sql
   ├── includes/
   │   └── db.php
   ├── assets/
   │   ├── styles.css
   │   └── app.js
   └── ajax/
       ├── patients.php
       ├── appointments.php
       ├── records.php
       ├── import.php
       └── export.php
   ```

### 4. Database Configuration
1. Open `includes/db.php`
2. Update database credentials if needed (default settings work with XAMPP):
   ```php
   $host = 'localhost';
   $dbname = 'fcpc_clinic';
   $username = 'root';
   $password = '';
   ```

### 5. Access the System
1. Open your browser
2. Navigate to: `http://localhost/clinic-system/`
3. The system is ready to use!

## Usage Guide

### Dashboard
- **Today's Appointments**: Real-time tracker showing all appointments for current date
- **Statistics**: Live count of total patients, today's appointments, and checked-in patients
- **Filters**: Sort by time or name, filter by appointment status
- **Quick Actions**: Check-in patients directly from dashboard

### Patients Tab
- **Add Patient**: Click "Add Patient" button
  - Select patient type (Student/Employee)
  - Fill in required information
  - Student fields: Student ID, Year Level
  - Employee fields: Employee ID, Department
- **Search**: Use search bar to find patients by first or last name
- **Filter**: Filter by patient type (Student/Employee/All)
- **Edit**: Click "Edit" button on any patient row
- **Delete**: Click "Delete" → Type "First City Providential College" → Confirm twice

### Appointments Tab
- **Create Appointment**: Click "New Appointment" button
  - Select patient from dropdown
  - Choose date and time
  - Select appointment type
  - Set initial status
  - Add optional notes
- **Filters**: 
  - Filter by year
  - Filter by patient type (Student/Employee)
  - Filter by status (Pending/Confirmed/Checked-in/Completed/Cancelled)
- **Edit**: Modify appointment details
- **Delete**: Requires college name confirmation

### Records Tab
- **View History**: Access all archived appointments (20-year retention)
- **Filters**:
  - Select year to view records from specific year
  - Filter by patient type
- **Auto-archiving**: Completed appointments are automatically added to records

### Import/Export
#### Import Data
1. Select import type (Patients or Appointments)
2. Choose file (Excel .xlsx/.xls, CSV .csv, or SQL .sql)
3. Click "Import Data"
4. System shows progress and completion status

**Import File Format Examples:**

**Patients CSV:**
```csv
patient_type,first_name,last_name,age,gender,phone,email,student_id,year_level
Student,John,Doe,20,Male,09123456789,john@example.com,2024-001,2nd Year
Employee,Jane,Smith,35,Female,09987654321,jane@example.com,,
```

**Appointments CSV:**
```csv
patient_id,appointment_date,appointment_time,appointment_type,status,notes
1,2024-01-31,09:00:00,Regular Checkup,pending,Annual checkup
2,2024-01-31,10:30:00,Consultation,confirmed,Follow-up visit
```

#### Export Data
1. Select export type (Patients/Appointments/Records)
2. Choose format (Excel/CSV/SQL)
3. Optionally select year range
4. Click "Export Data"
5. File downloads automatically

## Database Schema

### Tables
- **patients**: Patient information (students and employees)
- **appointments**: Appointment scheduling and tracking
- **appointment_records**: Historical archive (auto-populated by trigger)
- **medical_records**: Medical history and diagnoses
- **system_settings**: System configuration

### Key Features
- **Soft Delete**: `is_deleted` flag instead of permanent deletion
- **Auto-timestamps**: Created/updated timestamps on all records
- **Foreign Keys**: Data integrity with cascading deletes
- **Trigger**: Auto-archive completed appointments to records table

## Notification System

The notification bell icon shows:
- **Badge Number**: Count of pending (unconfirmed) appointments
- **Click to View**: Shows list of all pending appointments
- **Real-time Updates**: Badge updates when appointments are added/modified

## Security Best Practices

1. **Delete Confirmation**: 
   - First confirmation dialog appears
   - User must type "First City Providential College" exactly
   - Second confirmation required
   - Prevents accidental deletions

2. **POST-only Modifications**: All data changes use POST requests

3. **Soft Deletes**: Deleted records remain in database for recovery

4. **Input Validation**: All forms validate data before submission

## Troubleshooting

### Database Connection Error
- Check if MySQL is running in XAMPP
- Verify database credentials in `includes/db.php`
- Ensure `fcpc_clinic` database exists

### Import Not Working
- Check file format matches expected structure
- Ensure file size is under PHP upload limit
- Verify column headers match expected names

### Notifications Not Showing
- Check if there are pending appointments in database
- Refresh the page
- Clear browser cache

### Export Download Not Starting
- Ensure popup blocker is disabled
- Check browser download settings
- Try different export format

## File Structure

```
clinic-system/
├── index.php                 # Main application file
├── database.sql             # Database schema and setup
├── README.md               # This file
├── includes/
│   └── db.php              # Database connection
├── assets/
│   ├── styles.css          # Application styling
│   └── app.js             # Frontend JavaScript
└── ajax/
    ├── patients.php        # Patient CRUD operations
    ├── appointments.php    # Appointment management
    ├── records.php        # Historical records
    ├── import.php         # Data import handler
    └── export.php         # Data export handler
```

## Support

For issues or questions:
1. Check this README for solutions
2. Review browser console for JavaScript errors
3. Check Apache error logs in XAMPP
4. Verify database connection and structure

## Version History

**Version 1.0.0** (January 2024)
- Initial release
- Patient management (Student/Employee)
- Appointment scheduling
- Real-time dashboard
- Historical records (20-year retention)
- Import/Export functionality
- Secure delete confirmation

## License

Developed for First City Providential College
© 2024 FCPC. All rights reserved.
