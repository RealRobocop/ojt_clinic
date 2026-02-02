<?php
/**
 * MediCare - Clinic Management System
 * Main Dashboard Page with PHP Backend and Authentication
 * Compatible with XAMPP (Apache + MySQL/MariaDB)
 */

session_start();

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}

// Database connection
require_once 'includes/db.php';

// Get statistics
$stats = [];
try {
    // Count only active (non-deleted) records
    $result = $pdo->query("SELECT COUNT(*) as total FROM patients WHERE is_deleted = 0");
    $stats['totalPatients'] = $result->fetch()['total'];
    
    $result = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE is_deleted = 0");
    $stats['totalAppointments'] = $result->fetch()['total'];
    
    $result = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'checked-in' AND is_deleted = 0");
    $stats['totalCheckIns'] = $result->fetch()['total'];
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Get recent patients (active only)
$recentPatients = [];
try {
    $result = $pdo->query("
        SELECT * FROM patients 
        WHERE is_deleted = 0
        ORDER BY dateAdded DESC LIMIT 3
    ");
    $recentPatients = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching patients: " . $e->getMessage();
}

$doctor_name = isset($_SESSION['doctor_name']) ? $_SESSION['doctor_name'] : 'Doctor';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare - Clinic Management System</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="clinic-container">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="clinic-logo">
                    <div class="logo-icon">M</div>
                    <div class="logo-text">
                        <h2>MediCare</h2>
                        <p>Clinic Management</p>
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
            </nav>

            <div class="sidebar-footer">
                <button class="logout-btn" onclick="logout()">
                    <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
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
                    <input type="text" placeholder="Search patients..." id="searchInput" onkeyup="searchPatients()">
                </div>

                <div class="header-actions">
                    <div class="user-menu" onclick="showUserMenu()">
                        <div class="user-avatar"><?php echo strtoupper(substr($doctor_name, 0, 2)); ?></div>
                        <div class="user-info">
                            <p class="user-name">Dr. <?php echo htmlspecialchars($doctor_name); ?></p>
                            <p class="user-role">Doctor</p>
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
                            <p>Welcome back, Dr. <?php echo htmlspecialchars($doctor_name); ?></p>
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
                                <div class="stat-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value"><?php echo $stats['totalPatients']; ?></div>
                            <p class="stat-change">Active patients</p>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <h3>Appointments</h3>
                                <div class="stat-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value"><?php echo $stats['totalAppointments']; ?></div>
                            <p class="stat-change">Scheduled</p>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <h3>Check-ins</h3>
                                <div class="stat-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value"><?php echo $stats['totalCheckIns']; ?></div>
                            <p class="stat-change">Completed</p>
                        </div>
                    </div>

                    <!-- Recent Patients -->
                    <div class="content-grid">
                        <div class="card">
                            <div class="card-header">
                                <h2>Recent Patients</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-wrapper">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Patient Name</th>
                                                <th>Age</th>
                                                <th>Contact</th>
                                                <th>Last Visit</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentPatients as $patient): ?>
                                            <tr>
                                                <td>
                                                    <div class="patient-name">
                                                        <div class="patient-avatar"><?php echo strtoupper(substr($patient['name'], 0, 2)); ?></div>
                                                        <?php echo htmlspecialchars($patient['name']); ?>
                                                    </div>
                                                </td>
                                                <td><?php echo $patient['age']; ?></td>
                                                <td><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></td>
                                                <td><?php echo $patient['dateAdded']; ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn-table" title="View" onclick="viewPatient(<?php echo $patient['id']; ?>)">
                                                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
                                                        </button>
                                                        <button class="btn-table" title="Edit" onclick="editPatient(<?php echo $patient['id']; ?>)">
                                                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                            </svg>
                                                        </button>
                                                        <button class="btn-table danger" title="Delete" onclick="deletePatient(<?php echo $patient['id']; ?>)">
                                                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- PATIENTS SECTION -->
                <section class="section" id="patients">
                    <div class="page-header">
                        <div>
                            <h1>Patients</h1>
                            <p>Manage all patient records</p>
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
                        </div>
                        <div class="card-body">
                            <div class="table-wrapper">
                                <table id="patientsTable">
                                    <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Age</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Date Added</th>
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
                            <h2>Upcoming Appointments</h2>
                        </div>
                        <div class="card-body">
                            <div id="appointmentsList" class="appointments-list">
                                <!-- Loaded via AJAX -->
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
            <form id="patientForm" onsubmit="savePatient(event)">
                <input type="hidden" id="patientId">
                <div class="form-group">
                    <label for="patientName">Full Name *</label>
                    <input type="text" id="patientName" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="patientAge">Age *</label>
                        <input type="number" id="patientAge" min="0" max="150" required>
                    </div>
                    <div class="form-group">
                        <label for="patientGender">Gender *</label>
                        <select id="patientGender" required>
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
                        <input type="tel" id="patientPhone">
                    </div>
                    <div class="form-group">
                        <label for="patientEmail">Email</label>
                        <input type="email" id="patientEmail">
                    </div>
                </div>
                <div class="form-group">
                    <label for="patientAddress">Address</label>
                    <input type="text" id="patientAddress">
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
            <form id="appointmentForm" onsubmit="saveAppointment(event)">
                <input type="hidden" id="appointmentId">
                <div class="form-group">
                    <label for="appointmentPatient">Patient *</label>
                    <select id="appointmentPatient" required>
                        <option value="">Select Patient</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="appointmentDate">Date *</label>
                        <input type="date" id="appointmentDate" required>
                    </div>
                    <div class="form-group">
                        <label for="appointmentTime">Time *</label>
                        <input type="time" id="appointmentTime" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="appointmentType">Type *</label>
                    <select id="appointmentType" required>
                        <option value="">Select Type</option>
                        <option value="Regular Checkup">Regular Checkup</option>
                        <option value="Follow-up Visit">Follow-up Visit</option>
                        <option value="Consultation">Consultation</option>
                        <option value="Vaccination">Vaccination</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointmentStatus">Status *</label>
                    <select id="appointmentStatus" required>
                        <option value="">Select Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="checked-in">Checked In</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointmentNotes">Notes</label>
                    <textarea id="appointmentNotes" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAppointmentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Appointment</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/app.js"></script>
</body>
</html>