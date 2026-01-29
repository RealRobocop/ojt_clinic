# XAMPP Setup Guide - MediCare Clinic Management System (PHP/SQL)

Complete guide for setting up MediCare with XAMPP, PHP, and MySQL/MariaDB.

## 📋 Table of Contents

1. [Quick Start (5 minutes)](#quick-start)
2. [XAMPP Installation](#xampp-installation)
3. [Project Setup](#project-setup)
4. [Database Setup](#database-setup)
5. [Testing](#testing)
6. [Troubleshooting](#troubleshooting)

---

## 🚀 Quick Start

### 1. Download Project Files
Download all files from outputs folder

### 2. Extract to XAMPP
```bash
# Windows
C:\xampp\htdocs\mediclinic\

# Linux
/opt/lampp/htdocs/mediclinic/

# Mac
/Applications/XAMPP/htdocs/mediclinic/
```

### 3. Create Database
```sql
CREATE DATABASE clinic_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Import Schema
Run `database.sql` in phpMyAdmin

### 5. Access Application
Open: `http://localhost/mediclinic/`

---

## 💾 XAMPP Installation

### Windows

1. **Download XAMPP**
   - Go to: https://www.apachefriends.org/
   - Download PHP 7.4+ version

2. **Install**
   - Run installer
   - Select components: Apache, MySQL, PHP
   - Choose installation folder (default: `C:\xampp`)

3. **Start Services**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

4. **Verify Installation**
   - Open browser
   - Go to: `http://localhost`
   - You should see XAMPP dashboard

### Linux (Ubuntu/Debian)

```bash
# Install
sudo apt-get install xampp

# Or download from website and install
sudo ./xampp-linux-installer.run

# Start XAMPP
sudo /opt/lampp/bin/lampp start

# Verify
http://localhost
```

### macOS

```bash
# Download .dmg from apachefriends.org
# Double-click to mount
# Drag XAMPP folder to Applications

# Start from Applications or:
/Applications/XAMPP/manager-osx.app

# Or command line:
sudo /Applications/XAMPP/xamppfiles/bin/lampp start
```

---

## 📁 Project Setup

### 1. Create Project Folder

**Windows:**
```bash
C:\xampp\htdocs\mediclinic\
```

**Linux:**
```bash
/opt/lampp/htdocs/mediclinic/
```

**macOS:**
```bash
/Applications/XAMPP/htdocs/mediclinic/
```

### 2. Directory Structure

```
mediclinic/
├── index.php                 # Main page
├── login.php                 # Login page (optional)
├── database.sql              # SQL schema
├── assets/
│   ├── styles.css            # Stylesheet
│   ├── app.js                # Frontend JavaScript
│   └── images/               # Images folder
├── api/
│   ├── patients.php          # Patient API endpoints
│   ├── appointments.php      # Appointment API endpoints
│   └── config.php            # API configuration
├── includes/
│   ├── db.php                # Database connection
│   ├── functions.php         # Helper functions
│   └── header.php            # Header template
├── pages/
│   ├── dashboard.php         # Dashboard template
│   ├── patients.php          # Patients template
│   └── appointments.php      # Appointments template
└── uploads/                  # File uploads folder
```

### 3. Copy Files

1. Extract all downloaded files
2. Copy to XAMPP htdocs folder
3. Make sure file structure matches above

### 4. Set Permissions (Linux/Mac)

```bash
# Navigate to project
cd /opt/lampp/htdocs/mediclinic

# Set permissions
chmod -R 755 .
chmod -R 777 uploads/
chmod -R 777 includes/
```

---

## 🗄️ Database Setup

### Method 1: phpMyAdmin (Recommended for Beginners)

1. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`
   - Username: `root`
   - Password: (empty)

2. **Create Database**
   - Click "New"
   - Enter name: `clinic_management`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Import SQL**
   - Select database: `clinic_management`
   - Click "Import"
   - Choose `database.sql` file
   - Click "Go"

4. **Verify**
   - You should see 2 tables: `patients` and `appointments`

### Method 2: Command Line

**Windows (MySQL Command Prompt):**
```bash
# Connect to MySQL
mysql -u root -p

# Create database
CREATE DATABASE clinic_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinic_management;

# Import SQL file
source C:\xampp\htdocs\mediclinic\database.sql;

# Verify
SHOW TABLES;
```

**Linux/Mac:**
```bash
# Navigate to project folder
cd /opt/lampp/htdocs/mediclinic

# Import directly
mysql -u root < database.sql

# Or connect first
mysql -u root
> CREATE DATABASE clinic_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> USE clinic_management;
> source database.sql;
> SHOW TABLES;
```

### Method 3: PHP Script (auto-setup)

Create `setup.php`:

```php
<?php
$conn = new mysqli('localhost', 'root', '', '');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS clinic_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error: " . $conn->error . "<br>";
}

// Select database
$conn->select_db('clinic_management');

// Read and execute SQL file
$sqlFile = file_get_contents('database.sql');
$queries = explode(';', $sqlFile);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) === TRUE) {
            echo "Query executed<br>";
        } else {
            echo "Error: " . $conn->error . "<br>";
        }
    }
}

echo "Database setup complete!";
$conn->close();
?>
```

Access: `http://localhost/mediclinic/setup.php`

---

## ⚙️ Configuration

### 1. Database Configuration (includes/db.php)

```php
<?php
define('DB_HOST', 'localhost');    // XAMPP default
define('DB_USER', 'root');          // XAMPP default
define('DB_PASS', '');              // XAMPP default (empty)
define('DB_NAME', 'clinic_management');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (PDOException $e) {
    die("Connection Error: " . $e->getMessage());
}
?>
```

### 2. Modify Database User (Optional)

For security, create a new database user:

**In phpMyAdmin:**

1. Go to "User accounts"
2. Click "Add user"
3. Username: `clinic_user`
4. Password: `clinic_password`
5. Select "Create database with same name"
6. Check "Grant all privileges"
7. Click "Go"

Then update `includes/db.php`:

```php
define('DB_USER', 'clinic_user');
define('DB_PASS', 'clinic_password');
```

### 3. API Configuration (api/config.php)

```php
<?php
// API Configuration
define('API_KEY', 'your-secret-api-key-here');
define('DEBUG_MODE', true); // Set to false in production
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 10485760); // 10MB

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
?>
```

---

## 🧪 Testing

### 1. Test Database Connection

Create `test_db.php`:

```php
<?php
require_once 'includes/db.php';

try {
    $result = $pdo->query("SELECT COUNT(*) as count FROM patients");
    $data = $result->fetch();
    echo "✓ Database connected! Patient count: " . $data['count'];
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
```

Access: `http://localhost/mediclinic/test_db.php`

### 2. Test API Endpoints

**Get all patients:**
```bash
curl http://localhost/mediclinic/api/patients.php/list
```

**Add patient (POST):**
```bash
curl -X POST http://localhost/mediclinic/api/patients.php/create \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "age": 45,
    "gender": "Male",
    "phone": "(555) 123-4567",
    "email": "john@email.com",
    "address": "123 Main St"
  }'
```

**Get patient:**
```bash
curl http://localhost/mediclinic/api/patients.php/get/1
```

### 3. Manual Testing in Browser

1. Open: `http://localhost/mediclinic/`
2. Test "Add Patient" button
3. Test "Add Appointment" button
4. Test "Edit" buttons
5. Test "Delete" buttons
6. Test search functionality

---

## 🐛 Troubleshooting

### "Cannot connect to database"

**Solution:**
```php
// Check in includes/db.php
// Make sure MySQL is running

// Windows: Check XAMPP Control Panel
// Linux: sudo /opt/lampp/bin/lampp status
// Mac: Check XAMPP Manager or control panel
```

### "Table doesn't exist"

**Solution:**
```sql
-- Check if database exists
SHOW DATABASES;

-- Check if tables exist
USE clinic_management;
SHOW TABLES;

-- If not, re-import database.sql
```

### "Access denied for user 'root'@'localhost'"

**Solution:**
```php
// XAMPP default credentials:
DB_USER = 'root'
DB_PASS = '' (empty)

// If you changed them:
// 1. Go to phpMyAdmin
// 2. User accounts
// 3. Reset root user password
```

### "API returns 404"

**Solution:**
```
Check .htaccess file for proper routing
Make sure API files are in correct folder:
mediclinic/api/patients.php
mediclinic/api/appointments.php
```

### "JavaScript errors in console"

**Solution:**
```
Check browser console (F12)
Make sure API_BASE in app.js is correct:
const API_BASE = './api';

If using subdirectory:
const API_BASE = './mediclinic/api';
```

### "Upload folder permission denied"

**Solution:**
```bash
# Linux/Mac
chmod -R 777 /opt/lampp/htdocs/mediclinic/uploads

# Or change owner
sudo chown -R nobody /opt/lampp/htdocs/mediclinic/uploads
```

### "CORS errors"

**Solution:**
Add to api files (already included):
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
```

---

## 🚀 Going Live (Development to Production)

### Before Deployment

```php
// In includes/db.php
// Change localhost to production server
define('DB_HOST', 'your-database-server.com');

// In api/config.php
// Set DEBUG_MODE to false
define('DEBUG_MODE', false);

// Change database credentials
define('DB_USER', 'prod_clinic_user');
define('DB_PASS', 'strong-password-here');
```

### Security Checklist

- [ ] Change default MySQL password
- [ ] Use HTTPS (SSL certificate)
- [ ] Disable phpMyAdmin on production
- [ ] Create read-only database user for API
- [ ] Add API authentication (JWT tokens)
- [ ] Validate all user inputs
- [ ] Use prepared statements (already done)
- [ ] Set proper file permissions
- [ ] Regular database backups
- [ ] Update PHP to latest version

---

## 📊 PHP Version Compatibility

| PHP Version | Support | Notes |
|-------------|---------|-------|
| 5.6 | ❌ No | Too old |
| 7.0 | ⚠️ Limited | Basic support |
| 7.1 | ⚠️ Limited | Basic support |
| 7.2 | ✅ Full | Recommended |
| 7.3 | ✅ Full | Recommended |
| 7.4 | ✅ Full | **Best choice** |
| 8.0 | ✅ Full | Latest |
| 8.1+ | ✅ Full | Latest |

**Recommended:** PHP 7.4 or 8.0+

Check your version:
```php
<?php
echo phpversion();
?>
```

---

## 📚 Useful Commands

### Start/Stop XAMPP

**Windows:**
```bash
# Start
C:\xampp\xampp_start.exe

# Stop
C:\xampp\xampp_stop.exe
```

**Linux:**
```bash
# Start
sudo /opt/lampp/bin/lampp start

# Stop
sudo /opt/lampp/bin/lampp stop

# Restart
sudo /opt/lampp/bin/lampp restart
```

**macOS:**
```bash
# Start
sudo /Applications/XAMPP/xamppfiles/bin/lampp start

# Stop
sudo /Applications/XAMPP/xamppfiles/bin/lampp stop
```

### MySQL Commands

```bash
# Connect
mysql -u root -p

# List databases
SHOW DATABASES;

# Use database
USE clinic_management;

# Show tables
SHOW TABLES;

# Backup
mysqldump -u root -p clinic_management > backup.sql

# Restore
mysql -u root -p clinic_management < backup.sql

# Export data
SELECT * INTO OUTFILE '/tmp/patients.csv' 
FIELDS TERMINATED BY ',' FROM patients;
```

---

## 🔗 Useful Links

- [XAMPP Official](https://www.apachefriends.org/)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MariaDB Documentation](https://mariadb.com/docs/)
- [Apache Documentation](https://httpd.apache.org/docs/)

---

## ✅ Verification Checklist

After setup, verify everything works:

- [ ] XAMPP is running (Apache + MySQL)
- [ ] Database created: `clinic_management`
- [ ] Tables created: `patients`, `appointments`
- [ ] Sample data inserted
- [ ] index.php accessible at `http://localhost/mediclinic/`
- [ ] Dashboard displays statistics correctly
- [ ] Can add patient via form
- [ ] Can create appointment
- [ ] Can edit records
- [ ] Can delete records
- [ ] Search functionality works
- [ ] API endpoints return JSON
- [ ] No errors in browser console
- [ ] No errors in PHP error log

---

## 🎯 Next Steps

1. ✅ Complete setup
2. ✅ Test all features
3. ✅ Add sample data
4. ✅ Train users
5. ✅ Regular backups
6. ✅ Monitor performance

---

**XAMPP Version**: 7.4+ recommended  
**PHP Version**: 7.4+  
**MySQL Version**: 5.7+ or MariaDB 10.3+

Everything is ready for development and testing!
