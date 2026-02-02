<?php
/**
 * First City Providential College - Clinic Management System
 * Main Dashboard Page
 */

// Database connection
require_once 'includes/db.php';

// Get statistics
$stats = [];
try {
    // Count students and employees separately then combine
    $studentCount = $pdo->query("SELECT COUNT(*) as total FROM Students WHERE is_deleted = 0")->fetch()['total'];
    $employeeCount = $pdo->query("SELECT COUNT(*) as total FROM Employees WHERE is_deleted = 0")->fetch()['total'];
    $stats['totalPatients'] = $studentCount + $employeeCount;
    
    $result = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE is_deleted = 0");
    $stats['totalAppointments'] = $result->fetch()['total'];
    
    $result = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'checked-in' AND is_deleted = 0");
    $stats['totalCheckIns'] = $result->fetch()['total'];
    
    // Get unconfirmed appointments count for notification
    $result = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'pending' AND is_deleted = 0");
    $stats['pendingAppointments'] = $result->fetch()['total'];
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCPC Clinic Management System</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="clinic-container">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="clinic-logo">
                    <img src="assets/logo.png" alt="FCPC Logo" class="logo-image">
                    <div class="logo-text">
                        <h2>FCPC Clinic</h2>
                        <p>Management System</p>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <button class="nav-item active" data-section="dashboard" onclick="navigateTo('dashboard')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </button>
                <button class="nav-item" data-section="patients" onclick="navigateTo('patients')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Patients</span>
                </button>
                <button class="nav-item" data-section="appointments" onclick="navigateTo('appointments')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Appointments</span>
                </button>
                <button class="nav-item" data-section="records" onclick="navigateTo('records')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>Records</span>
                </button>
                <button class="nav-item" data-section="import-export" onclick="navigateTo('import-export')">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Import/Export</span>
                </button>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <!-- HEADER -->
            <header class="header">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="header-search">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" placeholder="Search patients..." id="globalSearch">
                </div>

                <div class="header-actions">
                    <button class="notification-btn" onclick="showNotifications()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <?php if ($stats['pendingAppointments'] > 0): ?>
                        <span class="notification-badge"><?php echo $stats['pendingAppointments']; ?></span>
                        <?php endif; ?>
                    </button>
                    
                    <div class="user-menu">
                        <div class="user-avatar">FC</div>
                        <div class="user-info">
                            <p class="user-name">FCPC Clinic</p>
                            <p class="user-role">Admin</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <div class="page-content">
                <!-- DASHBOARD SECTION -->
                <section class="section active" id="dashboard">
                    <div class="page-header">
                        <div>
                            <h1>Dashboard</h1>
                            <p>Real-time patient tracking for today</p>
                        </div>
                        <button class="btn btn-primary" onclick="openAddAppointmentModal()">
                            <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>New Appointment</span>
                        </button>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <h3>Total Patients</h3>
                                <div class="stat-icon stat-icon-blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="stat-number"><?php echo $stats['totalPatients']; ?></p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <h3>Today's Appointments</h3>
                                <div class="stat-icon stat-icon-green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                            <p class="stat-number" id="todayAppointmentsCount">0</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <h3>Checked In</h3>
                                <div class="stat-icon stat-icon-gold">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9 11 12 14 22 4"></polyline>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="stat-number" id="checkedInCount">0</p>
                        </div>
                    </div>

                    <!-- Today's Appointments Tracker -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Today's Appointments Tracker</h2>
                            <div class="filter-controls">
                                <select id="dashboardStatusFilter" onchange="filterDashboardAppointments()">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="checked-in">Checked In</option>
                                </select>
                                <select id="dashboardSortBy" onchange="filterDashboardAppointments()">
                                    <option value="time-asc">Time (Earliest First)</option>
                                    <option value="time-desc">Time (Latest First)</option>
                                    <option value="name-asc">Name (A-Z)</option>
                                    <option value="name-desc">Name (Z-A)</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-wrapper">
                                <table id="todayAppointmentsTable">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Patient Name</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="todayAppointmentsList">
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tomorrow's Appointments Tracker -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Tomorrow's Appointments</h2>
                            <div class="filter-controls">
                                <select id="tomorrowSortBy" onchange="filterTomorrowAppointments()">
                                    <option value="time-asc">Time (Earliest First)</option>
                                    <option value="time-desc">Time (Latest First)</option>
                                    <option value="name-asc">Name (A-Z)</option>
                                    <option value="name-desc">Name (Z-A)</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-wrapper">
                                <table id="tomorrowAppointmentsTable">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Patient Name</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tomorrowAppointmentsList">
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- PATIENTS SECTION -->
                <section class="section" id="patients">
                    <div class="page-header">
                        <div>
                            <h1>Patients</h1>
                            <p>Manage patient records</p>
                        </div>
                        <button class="btn btn-primary" onclick="openAddPatientModal()">
                            <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Add Patient</span>
                        </button>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2>Patient Records</h2>
                            <div class="filter-controls">
                                <select id="patientTypeFilter" onchange="filterPatients()">
                                    <option value="">All Types</option>
                                    <option value="Student">Student</option>
                                    <option value="Employee">Employee</option>
                                </select>
                                <select id="patientEducationFilter" onchange="filterPatients()" style="display:none;">
                                    <option value="">All Levels</option>
                                    <option value="Elementary">Elementary</option>
                                    <option value="Junior High">Junior High</option>
                                    <option value="Senior High">Senior High</option>
                                    <option value="College">College</option>
                                </select>
                                <select id="patientEmployeeFilter" onchange="filterPatients()" style="display:none;">
                                    <option value="">All Types</option>
                                    <option value="Teacher">Teacher</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Administration">Administration</option>
                                </select>
                                <select id="patientDeptTrackFilter" onchange="filterPatients()" style="display:none;">
                                    <option value="">All Departments/Tracks</option>
                                    <!-- Options loaded dynamically based on selected type -->
                                </select>
                                <input type="text" id="patientSearch" placeholder="Search by name..." onkeyup="filterPatients()" class="search-input">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-wrapper">
                                <table id="patientsTable">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Age</th>
                                            <th>Contact</th>
                                            <th>Level/Department</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patientsList">
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- APPOINTMENTS SECTION -->
                <section class="section" id="appointments">
                    <div class="page-header">
                        <div>
                            <h1>Appointments</h1>
                            <p>Manage all clinic appointments</p>
                        </div>
                        <button class="btn btn-primary" onclick="openAddAppointmentModal()">
                            <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>New Appointment</span>
                        </button>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2>Appointments</h2>
                            <div class="filter-controls">
                                <select id="appointmentYearFilter" onchange="filterAppointments()">
                                    <option value="">All Years</option>
                                </select>
                                <select id="appointmentTypeFilter" onchange="filterAppointments()">
                                    <option value="">All Patient Types</option>
                                    <option value="Student">Student</option>
                                    <option value="Employee">Employee</option>
                                </select>
                                <select id="appointmentStatusFilter" onchange="filterAppointments()">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="checked-in">Checked In</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="appointmentsList" class="appointments-list">
                                <!-- Loaded via AJAX -->
                            </div>
                        </div>
                    </div>
                </section>

                <!-- RECORDS SECTION -->
                <section class="section" id="records">
                    <div class="page-header">
                        <div>
                            <h1>Historical Records & Statistics</h1>
                            <p>Archive of all appointments and statistical analysis</p>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="tabs-container">
                        <div class="tabs-nav">
                            <button class="tab-btn active" onclick="switchRecordsTab('records-tab')">
                                <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Past Records
                            </button>
                            <button class="tab-btn" onclick="switchRecordsTab('statistics-tab')">
                                <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                                Statistics
                            </button>
                        </div>

                        <!-- Records Tab -->
                        <div id="records-tab" class="tab-content active">
                            <div class="card">
                                <div class="card-header">
                                    <h2>Appointment Records Archive</h2>
                                    <div class="filter-controls">
                                        <button class="btn btn-primary" onclick="archiveCompletedAppointments()" style="margin-right: 10px;">
                                            <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                                <polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline>
                                                <polyline points="7.5 19.79 7.5 14.6 3 12"></polyline>
                                                <polyline points="21 12 16.5 14.6 16.5 19.79"></polyline>
                                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                            </svg>
                                            Archive Completed
                                        </button>
                                        <select id="recordsYearFilter" onchange="filterRecords()">
                                            <option value="">All Years</option>
                                        </select>
                                        <select id="recordsTypeFilter" onchange="filterRecords()">
                                            <option value="">All Patient Types</option>
                                            <option value="Student">Student</option>
                                            <option value="Employee">Employee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-wrapper">
                                        <table id="recordsTable">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Patient Name</th>
                                                    <th>Type</th>
                                                    <th>Level/Department</th>
                                                    <th>Appointment Type</th>
                                                    <th>Status</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recordsList">
                                                <!-- Loaded via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Tab -->
                        <div id="statistics-tab" class="tab-content">
                            <div class="stats-grid">
                                <!-- Gender Ratio Chart -->
                                <div class="card">
                                    <div class="card-header">
                                        <h2>Gender Distribution</h2>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="genderChart"></canvas>
                                        <div id="genderStats" class="stats-summary"></div>
                                    </div>
                                </div>

                                <!-- Grade Level Chart -->
                                <div class="card">
                                    <div class="card-header">
                                        <h2>Most Appointments by Education Level</h2>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="educationChart"></canvas>
                                        <div id="educationStats" class="stats-summary"></div>
                                    </div>
                                </div>

                                <!-- Employee Type Chart -->
                                <div class="card">
                                    <div class="card-header">
                                        <h2>Employee Appointments by Type</h2>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="employeeChart"></canvas>
                                        <div id="employeeStats" class="stats-summary"></div>
                                    </div>
                                </div>

                                <!-- Course/Track Distribution -->
                                <div class="card">
                                    <div class="card-header">
                                        <h2>Top 10 Courses/Tracks</h2>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="courseChart"></canvas>
                                        <div id="courseStats" class="stats-summary"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- IMPORT/EXPORT SECTION -->
                <section class="section" id="import-export">
                    <div class="page-header">
                        <div>
                            <h1>Import/Export Data</h1>
                            <p>Manage data import and export</p>
                        </div>
                    </div>

                    <div class="content-grid">
                        <!-- Import Section -->
                        <div class="card">
                            <div class="card-header">
                                <h2>Import Data</h2>
                            </div>
                            <div class="card-body">
                                <form id="importForm" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="importType">Import Type *</label>
                                        <select id="importType" name="importType" required>
                                            <option value="">Select Import Type</option>
                                            <option value="patients">Patients</option>
                                            <option value="appointments">Appointments</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="importFile">Select File (Excel, CSV, SQL) *</label>
                                        <input type="file" id="importFile" name="importFile" 
                                               accept=".xlsx,.xls,.csv,.sql" required>
                                        <small>Supported formats: Excel (.xlsx, .xls), CSV (.csv), SQL (.sql)</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                        <span>Import Data</span>
                                    </button>
                                </form>
                                <div id="importProgress" class="import-progress" style="display: none;">
                                    <div class="progress-bar">
                                        <div class="progress-fill" id="importProgressBar"></div>
                                    </div>
                                    <p id="importStatus">Importing...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Export Section -->
                        <div class="card">
                            <div class="card-header">
                                <h2>Export Data</h2>
                            </div>
                            <div class="card-body">
                                <form id="exportForm">
                                    <div class="form-group">
                                        <label for="exportType">Export Type *</label>
                                        <select id="exportType" name="exportType" required>
                                            <option value="">Select Export Type</option>
                                            <option value="patients">Patients</option>
                                            <option value="appointments">Appointments</option>
                                            <option value="records">Historical Records</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exportFormat">Export Format *</label>
                                        <select id="exportFormat" name="exportFormat" required>
                                            <option value="excel">Excel (.xlsx)</option>
                                            <option value="csv">CSV (.csv)</option>
                                            <option value="sql">SQL Database</option>
                                        </select>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="exportYearFrom">From Year</label>
                                            <select id="exportYearFrom" name="exportYearFrom">
                                                <option value="">All Years</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exportYearTo">To Year</label>
                                            <select id="exportYearTo" name="exportYearTo">
                                                <option value="">All Years</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        <span>Export Data</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- MODALS -->

    <!-- Add/Edit Patient Modal -->
    <div class="modal" id="patientModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="patientModalTitle">Add New Patient</h2>
                <button class="modal-close" onclick="closePatientModal()">&times;</button>
            </div>
            <form id="patientForm">
                <input type="hidden" id="patientId" name="patientId">
                <div class="form-group">
                    <label for="patientType">Patient Type *</label>
                    <select id="patientType" name="patientType" required onchange="togglePatientFields()">
                        <option value="">Select Type</option>
                        <option value="Student">Student</option>
                        <option value="Employee">Employee</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="patientAge">Age *</label>
                        <input type="number" id="patientAge" name="age" min="0" max="150" required>
                    </div>
                    <div class="form-group">
                        <label for="patientGender">Gender *</label>
                        <select id="patientGender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="patientPhone">Phone</label>
                        <input type="tel" id="patientPhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="patientEmail">Email</label>
                        <input type="email" id="patientEmail" name="email">
                    </div>
                </div>
                
                <!-- Student Fields -->
                <div id="studentFields" style="display: none;">
                    <div class="form-group">
                        <label for="studentId">Student ID *</label>
                        <input type="text" id="studentId" name="studentId">
                    </div>
                    <div class="form-group">
                        <label for="educationLevel">Education Level *</label>
                        <select id="educationLevel" name="educationLevel" onchange="updateGradeLevelOptions()">
                            <option value="">Select Level</option>
                            <option value="Elementary">Elementary</option>
                            <option value="Junior High">Junior High</option>
                            <option value="Senior High">Senior High</option>
                            <option value="College">College</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gradeLevel">Grade Level *</label>
                        <select id="gradeLevel" name="gradeLevel">
                            <option value="">Select Grade/Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="courseTrack">Course/Track *</label>
                        <select id="courseTrack" name="courseTrack">
                            <option value="">Select Course/Track</option>
                        </select>
                    </div>
                </div>
                
                <!-- Employee Fields -->
                <div id="employeeFields" style="display: none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="employeeId">Employee ID *</label>
                            <input type="text" id="employeeId" name="employeeId">
                        </div>
                        <div class="form-group">
                            <label for="employeeType">Employee Type *</label>
                            <select id="employeeType" name="employeeType">
                                <option value="">Select Type</option>
                                <option value="Teacher">Teacher</option>
                                <option value="Staff">Staff</option>
                                <option value="Administration">Administration</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="department">Department *</label>
                        <!-- 
                        ==========================================
                        EMPLOYEE DEPARTMENTS - EDIT HERE TO ADD MORE
                        ==========================================
                        To add more departments:
                        1. Add new <option value="Department Name">Department Name</option>
                        2. Keep alphabetical order for easy management
                        3. Use clear, descriptive names
                        ==========================================
                        -->
                        <select id="department" name="department">
                            <option value="">Select Department</option>
                            
                            <!-- Academic Departments -->
                            <optgroup label="Academic Departments">
                                <option value="Arts and Sciences">Arts and Sciences</option>
                                <option value="Business Administration">Business Administration</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Education">Education</option>
                                <option value="Engineering">Engineering</option>
                                <option value="English Department">English Department</option>
                                <option value="Filipino Department">Filipino Department</option>
                                <option value="Hospitality Management">Hospitality Management</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Mathematics Department">Mathematics Department</option>
                                <option value="Nursing">Nursing</option>
                                <option value="Science Department">Science Department</option>
                                <option value="Social Studies Department">Social Studies Department</option>
                                <option value="Tourism Management">Tourism Management</option>
                            </optgroup>
                            
                            <!-- Administrative Departments -->
                            <optgroup label="Administrative Departments">
                                <option value="Accounting Office">Accounting Office</option>
                                <option value="Admissions Office">Admissions Office</option>
                                <option value="Cashier Office">Cashier Office</option>
                                <option value="Clinic/Health Services">Clinic/Health Services</option>
                                <option value="Guidance Office">Guidance Office</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Library Services">Library Services</option>
                                <option value="Office of the President">Office of the President</option>
                                <option value="Office of the Registrar">Office of the Registrar</option>
                                <option value="Public Relations">Public Relations</option>
                                <option value="Scholarship Office">Scholarship Office</option>
                                <option value="Student Affairs">Student Affairs</option>
                            </optgroup>
                            
                            <!-- Support Services -->
                            <optgroup label="Support Services">
                                <option value="Facilities Management">Facilities Management</option>
                                <option value="Grounds and Maintenance">Grounds and Maintenance</option>
                                <option value="ICT/IT Department">ICT/IT Department</option>
                                <option value="Janitorial Services">Janitorial Services</option>
                                <option value="Security Office">Security Office</option>
                            </optgroup>
                            
                            <!-- Special Offices -->
                            <optgroup label="Special Offices">
                                <option value="Athletic Department">Athletic Department</option>
                                <option value="Cultural Affairs">Cultural Affairs</option>
                                <option value="Research and Development">Research and Development</option>
                                <option value="NSTP Office">NSTP Office</option>
                                <option value="Quality Assurance">Quality Assurance</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="patientAddress">Address</label>
                    <input type="text" id="patientAddress" name="address">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePatientModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Patient</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add/Edit Appointment Modal -->
    <div class="modal" id="appointmentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="appointmentModalTitle">Add New Appointment</h2>
                <button class="modal-close" onclick="closeAppointmentModal()">&times;</button>
            </div>
            <form id="appointmentForm">
                <input type="hidden" id="appointmentId" name="appointmentId">
                <input type="hidden" id="appointmentPatientType" name="patientType">
                <div class="form-group">
                    <label for="appointmentPatient">Patient *</label>
                    <select id="appointmentPatient" name="patientId" required onchange="updateAppointmentPatientType()">
                        <option value="">Select Patient</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="appointmentDate">Date *</label>
                        <input type="date" id="appointmentDate" name="appointmentDate" required>
                    </div>
                    <div class="form-group">
                        <label for="appointmentTime">Time *</label>
                        <input type="time" id="appointmentTime" name="appointmentTime" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="appointmentType">Type *</label>
                    <select id="appointmentType" name="appointmentType" required>
                        <option value="">Select Type</option>
                        <option value="Regular Checkup">Regular Checkup</option>
                        <option value="Follow-up Visit">Follow-up Visit</option>
                        <option value="Consultation">Consultation</option>
                        <option value="Vaccination">Vaccination</option>
                        <option value="Medical Certificate">Medical Certificate</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointmentStatus">Status *</label>
                    <select id="appointmentStatus" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="checked-in">Checked In</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointmentNotes">Notes</label>
                    <textarea id="appointmentNotes" name="notes" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAppointmentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Appointment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h2>Confirm Deletion</h2>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage">Are you sure you want to delete this item?</p>
                <div class="form-group">
                    <label for="deleteConfirmText">Type "First City Providential College" to confirm:</label>
                    <input type="text" id="deleteConfirmText" class="form-control" placeholder="Enter college name">
                    <small class="text-danger" id="deleteConfirmError" style="display: none;">Incorrect text. Please try again.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()" id="confirmDeleteBtn" disabled>Delete</button>
            </div>
        </div>
    </div>

    <!-- Notification Panel -->
    <div class="notification-panel" id="notificationPanel">
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="modal-close" onclick="closeNotifications()">&times;</button>
        </div>
        <div class="notification-body" id="notificationList">
            <!-- Loaded via AJAX -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="assets/app.js"></script>
</body>
</html>
