# Quick Start Guide - FCPC Clinic Management System

## Getting Started in 5 Minutes

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install and start **Apache** and **MySQL** services

### Step 2: Setup Database
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click **Import** tab
3. Click **Choose File** and select `database.sql`
4. Click **Go** button
5. Wait for success message

### Step 3: Add Sample Data (Optional)
1. In phpMyAdmin, click **Import** again
2. Select `sample_data.sql`
3. Click **Go**
4. Now you have test patients and appointments!

### Step 4: Deploy Files
1. Copy the entire `clinic-system` folder
2. Paste into: `C:\xampp\htdocs\`

### Step 5: Access System
1. Open browser
2. Navigate to: `http://localhost/clinic-system/`
3. You're ready to go! 🎉

## First Tasks to Try

### 1. View Today's Dashboard
- The dashboard shows all appointments scheduled for today
- Try the sort and filter options
- Click "Check In" on any pending appointment

### 2. Add Your First Patient
1. Click **Patients** in sidebar
2. Click **Add Patient** button
3. Select patient type (Student or Employee)
4. Fill in the form
5. Click **Save Patient**

### 3. Create an Appointment
1. Click **Appointments** in sidebar
2. Click **New Appointment** button
3. Select a patient from dropdown
4. Choose date, time, and type
5. Click **Save Appointment**

### 4. Test the Delete Protection
1. Try to delete a patient or appointment
2. Notice you must type "First City Providential College" exactly
3. This prevents accidental deletions!

### 5. Check Notifications
1. Look at the bell icon in header
2. Number shows pending appointments
3. Click bell to see details

### 6. Explore Records
1. Click **Records** in sidebar
2. See all historical appointments
3. Filter by year or patient type
4. These records are kept for 20 years!

### 7. Try Import/Export
1. Click **Import/Export** in sidebar
2. Export patients to Excel
3. Try importing the exported file back

## Common Features

### Dashboard Filters
- **Status Filter**: Show only pending, confirmed, or checked-in
- **Sort**: By time (earliest/latest first) or by name (A-Z or Z-A)

### Patient Search
- Type any part of first or last name
- Results update instantly
- Filter by Student or Employee type

### Appointment Filters
- **Year**: View appointments from specific year
- **Patient Type**: Students or Employees only
- **Status**: Filter by appointment status

### Notification System
- Badge shows count of unconfirmed appointments
- Click bell icon to view details
- Updates automatically

## Tips and Tricks

1. **Quick Check-in**: From dashboard, click "Check In" button directly
2. **Fast Search**: Use the global search bar in header
3. **Bulk Export**: Export data filtered by year range
4. **Safe Delete**: System requires college name - prevents accidents
5. **Auto Archive**: Completed appointments automatically save to records

## Keyboard Shortcuts

- **Escape**: Close any open modal
- **Enter**: Submit active form
- **Tab**: Navigate between form fields

## Need Help?

Check the full README.md for:
- Detailed feature documentation
- Troubleshooting guide
- Database schema information
- Security features explanation

## Default Test Data

If you imported `sample_data.sql`, you'll have:
- **8 Patients**: 5 students and 3 employees
- **6 Today's Appointments**: Mix of pending, confirmed, checked-in
- **10 Future Appointments**: Scheduled for next few days
- **5 Past Appointments**: Recently completed
- **15 Historical Records**: From 2021-2023

## Video Tutorials (If Available)

1. System Overview (5 min)
2. Managing Patients (3 min)
3. Scheduling Appointments (4 min)
4. Using Import/Export (6 min)
5. Understanding Records (3 min)

---

**Ready to start?** Just open `http://localhost/clinic-system/` and begin! 🏥
