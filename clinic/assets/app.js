/**
 * First City Providential College - Clinic Management System
 * Main Application JavaScript - Updated Version
 */

// Global variables
let deleteType = null;
let deleteId = null;
let currentSection = 'dashboard';
let charts = {}; // Store chart instances

// ==========================================
// PHILIPPINE EDUCATION SYSTEM DATA
// ==========================================
// This object contains all courses, tracks, and strands based on DepEd/CHED
// TO ADD MORE COURSES/TRACKS/STRANDS:
// 1. Find the appropriate education level below
// 2. Add your course/track to the 'courses' array
// 3. Keep alphabetical order for easy management
// 4. Use official DepEd/CHED naming conventions
// ==========================================

const educationData = {
    Elementary: {
        grades: ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],
        courses: ['General Education']
    },
    
    'Junior High': {
        grades: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'],
        courses: ['General Education']
    },
    
    // ==========================================
    // SENIOR HIGH SCHOOL TRACKS/STRANDS
    // Based on DepEd K-12 Program
    // ==========================================
    'Senior High': {
        grades: ['Grade 11', 'Grade 12'],
        courses: [
            // Academic Track - STEM
            'STEM (Science, Technology, Engineering, Mathematics)',
            
            // Academic Track - ABM
            'ABM (Accountancy, Business, Management)',
            
            // Academic Track - HUMSS
            'HUMSS (Humanities and Social Sciences)',
            
            // Academic Track - GAS
            'GAS (General Academic Strand)',
            
            // TVL Track - ICT
            'TVL-ICT (Information and Communications Technology)',
            
            // TVL Track - Home Economics
            'TVL-HE (Home Economics)',
            'TVL-HE - Bread and Pastry Production',
            'TVL-HE - Cookery',
            'TVL-HE - Food and Beverage Services',
            'TVL-HE - Housekeeping',
            
            // TVL Track - Industrial Arts
            'TVL-IA (Industrial Arts)',
            'TVL-IA - Automotive Servicing',
            'TVL-IA - Carpentry',
            'TVL-IA - Construction',
            'TVL-IA - Electrical Installation and Maintenance',
            'TVL-IA - Electronics',
            'TVL-IA - Plumbing',
            'TVL-IA - Welding',
            
            // TVL Track - Agri-Fishery Arts
            'TVL-AFA (Agri-Fishery Arts)',
            'TVL-AFA - Agricultural Crops Production',
            'TVL-AFA - Animal Production',
            'TVL-AFA - Aquaculture',
            'TVL-AFA - Horticulture',
            'TVL-AFA - Landscape Installation',
            'TVL-AFA - Organic Agriculture',
            'TVL-AFA - Pest Management',
            
            // Arts and Design Track
            'Arts and Design',
            'Arts and Design - Media Arts',
            'Arts and Design - Visual Arts',
            
            // Sports Track
            'Sports Track'
        ]
    },
    
    // ==========================================
    // COLLEGE/UNIVERSITY COURSES
    // Based on CHED Programs
    // ==========================================
    College: {
        grades: ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'],
        courses: [
            // ========== BUSINESS AND MANAGEMENT ==========
            'BS Accountancy (BSA)',
            'BS Business Administration (BSBA)',
            'BS Business Administration - Financial Management',
            'BS Business Administration - Human Resource Management',
            'BS Business Administration - Marketing Management',
            'BS Business Administration - Operations Management',
            'BS Entrepreneurship (BSE)',
            'BS Office Administration (BSOA)',
            'BS Real Estate Management (BSREM)',
            
            // ========== COMPUTER STUDIES ==========
            'BS Computer Science (BSCS)',
            'BS Information Systems (BSIS)',
            'BS Information Technology (BSIT)',
            
            // ========== EDUCATION ==========
            'Bachelor of Elementary Education (BEEd)',
            'Bachelor of Early Childhood Education (BECEd)',
            'Bachelor of Physical Education (BPEd)',
            'Bachelor of Special Needs Education (BSNEd)',
            'Bachelor of Technology and Livelihood Education (BTLEd)',
            'BS Education (BSEd)',
            'BS Secondary Education - English (BSEd-English)',
            'BS Secondary Education - Filipino (BSEd-Filipino)',
            'BS Secondary Education - Mathematics (BSEd-Math)',
            'BS Secondary Education - Science (BSEd-Science)',
            'BS Secondary Education - Social Studies (BSEd-SocSci)',
            
            // ========== ENGINEERING ==========
            'BS Architecture (BSArch)',
            'BS Civil Engineering (BSCE)',
            'BS Computer Engineering (BSCpE)',
            'BS Electrical Engineering (BSEE)',
            'BS Electronics Engineering (BSECE)',
            'BS Industrial Engineering (BSIE)',
            'BS Mechanical Engineering (BSME)',
            
            // ========== HEALTH SCIENCES ==========
            'BS Medical Technology (BSMT)',
            'BS Midwifery (BSM)',
            'BS Nursing (BSN)',
            'BS Nutrition and Dietetics (BSND)',
            'BS Pharmacy (BSP)',
            'BS Physical Therapy (BSPT)',
            'BS Public Health (BSPH)',
            'BS Radiologic Technology (BSRT)',
            'BS Respiratory Therapy (BSRT)',
            'Doctor of Medicine (MD)',
            
            // ========== HOSPITALITY AND TOURISM ==========
            'BS Hotel and Restaurant Management (BSHRM)',
            'BS Hospitality Management (BSHM)',
            'BS Tourism Management (BSTM)',
            
            // ========== LIBERAL ARTS ==========
            'AB Communication (ABComm)',
            'AB English Language',
            'AB Filipino',
            'AB History',
            'AB Journalism',
            'AB Literature',
            'AB Philosophy',
            'AB Political Science (AB PolSci)',
            'AB Psychology (AB Psych)',
            'AB Sociology',
            'BS Psychology (BS Psych)',
            
            // ========== SCIENCES ==========
            'BS Agriculture (BSA)',
            'BS Applied Mathematics (BSAM)',
            'BS Biology (BSBio)',
            'BS Chemistry (BSChem)',
            'BS Environmental Science (BSES)',
            'BS Fisheries (BSF)',
            'BS Forestry (BSFor)',
            'BS Marine Biology (BSMB)',
            'BS Mathematics (BSMath)',
            'BS Physics (BSPhy)',
            
            // ========== CRIMINOLOGY ==========
            'BS Criminology (BSCrim)',
            
            // ========== SOCIAL WORK ==========
            'BS Social Work (BSSW)',
            
            // ========== MULTIMEDIA ARTS ==========
            'BS Entertainment and Multimedia Computing (BSEMC)',
            'BA Multimedia Arts (BAMMA)',
            
            // ========== OTHERS ==========
            'BS Customs Administration (BSCA)',
            'BS Development Communication (BSDC)',
            'BS Library and Information Science (BSLIS)',
            'BS Marine Transportation (BSMT)',
            'BS Maritime Engineering (BSMarE)'
        ]
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    loadPatientOptions();
    loadYearOptions();
    loadTodayAppointments();
    loadTomorrowAppointments();
    loadPatients();
    loadAppointments();
    
    // Setup form submissions
    setupFormHandlers();
    
    // Setup delete confirmation input
    setupDeleteConfirmation();
    
    // Auto-refresh dashboard every 30 seconds
    setInterval(() => {
        if (currentSection === 'dashboard') {
            loadTodayAppointments();
            loadTomorrowAppointments();
        }
    }, 30000);
});

function initializeApp() {
    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
        });
    }
    
    // Setup patient type filter change handler
    const patientTypeFilter = document.getElementById('patientTypeFilter');
    if (patientTypeFilter) {
        patientTypeFilter.addEventListener('change', function() {
            const educationFilter = document.getElementById('patientEducationFilter');
            const employeeFilter = document.getElementById('patientEmployeeFilter');
            const deptTrackFilter = document.getElementById('patientDeptTrackFilter');
            
            if (this.value === 'Student') {
                educationFilter.style.display = 'block';
                employeeFilter.style.display = 'none';
                deptTrackFilter.style.display = 'block';
                populateStudentDeptTrackFilter();
            } else if (this.value === 'Employee') {
                educationFilter.style.display = 'none';
                employeeFilter.style.display = 'block';
                deptTrackFilter.style.display = 'block';
                populateEmployeeDeptTrackFilter();
            } else {
                educationFilter.style.display = 'none';
                employeeFilter.style.display = 'none';
                deptTrackFilter.style.display = 'none';
            }
        });
    }
    
    // Setup education level filter to update dept/track options
    const educationFilter = document.getElementById('patientEducationFilter');
    if (educationFilter) {
        educationFilter.addEventListener('change', function() {
            populateStudentDeptTrackFilter();
        });
    }
}

// Populate department/track filter for students based on education level
function populateStudentDeptTrackFilter() {
    const educationLevel = document.getElementById('patientEducationFilter')?.value;
    const deptTrackFilter = document.getElementById('patientDeptTrackFilter');
    
    if (!deptTrackFilter) return;
    
    deptTrackFilter.innerHTML = '<option value="">All Courses/Tracks</option>';
    
    if (educationLevel && educationData[educationLevel]) {
        educationData[educationLevel].courses.forEach(course => {
            const option = document.createElement('option');
            option.value = course;
            option.textContent = course;
            deptTrackFilter.appendChild(option);
        });
    } else {
        // Show all student courses if no education level selected
        Object.keys(educationData).forEach(level => {
            educationData[level].courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course;
                option.textContent = `${level} - ${course}`;
                deptTrackFilter.appendChild(option);
            });
        });
    }
}

// Populate department filter for employees
function populateEmployeeDeptTrackFilter() {
    const deptTrackFilter = document.getElementById('patientDeptTrackFilter');
    
    if (!deptTrackFilter) return;
    
    // Get departments from the employee form dropdown
    const departmentSelect = document.getElementById('department');
    if (!departmentSelect) return;
    
    deptTrackFilter.innerHTML = '<option value="">All Departments</option>';
    
    // Copy options from employee department dropdown
    const optgroups = departmentSelect.querySelectorAll('optgroup');
    optgroups.forEach(optgroup => {
        const newOptgroup = document.createElement('optgroup');
        newOptgroup.label = optgroup.label;
        
        const options = optgroup.querySelectorAll('option');
        options.forEach(opt => {
            if (opt.value) {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.textContent;
                newOptgroup.appendChild(option);
            }
        });
        
        deptTrackFilter.appendChild(newOptgroup);
    });
}

// ========================================
// NAVIGATION
// ========================================
function navigateTo(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });

    // Show selected section
    const target = document.getElementById(sectionId);
    if (target) {
        target.classList.add('active');
    }

    // Update sidebar active state
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.classList.remove('active');
    });

    const activeLink = document.querySelector(`.sidebar a[data-section="${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }

    // Track current section
    currentSection = sectionId;

    // Load data depending on section
    if (sectionId === 'dashboard') {
        loadTodayAppointments();
        loadTomorrowAppointments();
    }

    if (sectionId === 'patients') {
        loadPatients();
    }

    if (sectionId === 'appointments') {
        loadAppointments();
    }
}
// ========================================
// DASHBOARD - TODAY'S & TOMORROW'S APPOINTMENTS
// ========================================
function loadTodayAppointments() {
    const formData = new FormData();
    formData.append('action', 'getTodayAppointments');
    
    const statusFilter = document.getElementById('dashboardStatusFilter')?.value || '';
    const sortBy = document.getElementById('dashboardSortBy')?.value || 'time-asc';
    
    if (statusFilter) formData.append('status', statusFilter);
    formData.append('sortBy', sortBy);
    
    fetch('./ajax/appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTodayAppointments(data.appointments);
            updateDashboardStats(data.stats);
        }
    })
    .catch(error => console.error('Error:', error));
}

function loadTomorrowAppointments() {
    const formData = new FormData();
    formData.append('action', 'getTomorrowAppointments');
    
    const sortBy = document.getElementById('tomorrowSortBy')?.value || 'time-asc';
    formData.append('sortBy', sortBy);
    
    fetch('./ajax/appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTomorrowAppointments(data.appointments);
        }
    })
    .catch(error => console.error('Error:', error));
}

function renderTodayAppointments(appointments) {
    const tbody = document.getElementById('todayAppointmentsList');
    
    if (!appointments || appointments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No appointments scheduled for today</td></tr>';
        return;
    }
    
    tbody.innerHTML = appointments.map(apt => `
        <tr>
            <td>${formatTime(apt.appointment_time)}</td>
            <td>${apt.patient_name}</td>
            <td>${apt.appointment_type}</td>
            <td><span class="badge badge-${apt.status}">${capitalizeFirst(apt.status)}</span></td>
            <td>
                <div class="action-buttons">
                    ${apt.status !== 'checked-in' && apt.status !== 'completed' ? `
                        <button class="btn btn-sm btn-primary" onclick="checkInAppointment(${apt.id})">
                            Check In
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-secondary" onclick="editAppointment(${apt.id})">
                        Edit
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderTomorrowAppointments(appointments) {
    const tbody = document.getElementById('tomorrowAppointmentsList');
    
    if (!appointments || appointments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No appointments scheduled for tomorrow</td></tr>';
        return;
    }
    
    tbody.innerHTML = appointments.map(apt => `
        <tr>
            <td>${formatTime(apt.appointment_time)}</td>
            <td>${apt.patient_name}</td>
            <td>${apt.appointment_type}</td>
            <td><span class="badge badge-${apt.status}">${capitalizeFirst(apt.status)}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-secondary" onclick="editAppointment(${apt.id})">
                        Edit
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function updateDashboardStats(stats) {
    if (stats) {
        document.getElementById('todayAppointmentsCount').textContent = stats.today || 0;
        document.getElementById('checkedInCount').textContent = stats.checkedIn || 0;
    }
}

function filterDashboardAppointments() {
    loadTodayAppointments();
}

function filterTomorrowAppointments() {
    loadTomorrowAppointments();
}

function checkInAppointment(id) {
    const formData = new FormData();
    formData.append('action', 'checkIn');
    formData.append('id', id);
    
    fetch('./ajax/appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Patient checked in successfully!', 'success');
            loadTodayAppointments();
            loadTomorrowAppointments();
        } else {
            showAlert(data.message || 'Error checking in patient', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error checking in patient', 'danger');
    });
}

// ========================================
// PATIENTS
// ========================================
function loadPatients() {
    const typeFilter = document.getElementById('patientTypeFilter')?.value || '';
    const educationFilter = document.getElementById('patientEducationFilter')?.value || '';
    const employeeFilter = document.getElementById('patientEmployeeFilter')?.value || '';
    const deptTrackFilter = document.getElementById('patientDeptTrackFilter')?.value || '';
    const searchQuery = document.getElementById('patientSearch')?.value || '';
    
    const formData = new FormData();
    formData.append('action', 'getPatients');
    formData.append('type', typeFilter);
    formData.append('educationLevel', educationFilter);
    formData.append('employeeType', employeeFilter);
    formData.append('deptTrack', deptTrackFilter);
    formData.append('search', searchQuery);
    
    fetch('ajax/patients.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderPatients(data.patients);
        }
    })
    .catch(error => console.error('Error:', error));
}

function renderPatients(patients) {
    const tbody = document.getElementById('patientsList');
    
    if (!patients || patients.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No patients found</td></tr>';
        return;
    }
    
    tbody.innerHTML = patients.map(patient => {
        let levelInfo = '';
        if (patient.patient_type === 'Student') {
            levelInfo = `${patient.education_level || 'N/A'} - ${patient.grade_level || 'N/A'}`;
        } else {
            levelInfo = `${patient.employee_type || 'N/A'} - ${patient.department || 'N/A'}`;
        }
        
        return `
        <tr>
            <td><span class="badge badge-${patient.patient_type.toLowerCase()}">${patient.patient_type}</span></td>
            <td>${patient.first_name}</td>
            <td>${patient.last_name}</td>
            <td>${patient.age || 'N/A'}</td>
            <td>${patient.phone || 'N/A'}</td>
            <td>${levelInfo}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-secondary" onclick="editPatient(${patient.id}, '${patient.patient_type}')">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="confirmDeletePatient(${patient.id}, '${patient.first_name} ${patient.last_name}', '${patient.patient_type}')">
                        Delete
                    </button>
                </div>
            </td>
        </tr>
        `;
    }).join('');
}

function filterPatients() {
    loadPatients();
}

function openAddPatientModal() {
    document.getElementById('patientModalTitle').textContent = 'Add New Patient';
    document.getElementById('patientForm').reset();
    document.getElementById('patientId').value = '';
    document.getElementById('patientModal').classList.add('active');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.remove('active');
}

function togglePatientFields() {
    const patientType = document.getElementById('patientType').value;
    const studentFields = document.getElementById('studentFields');
    const employeeFields = document.getElementById('employeeFields');
    
    if (patientType === 'Student') {
        studentFields.style.display = 'block';
        employeeFields.style.display = 'none';
        
        // Set student fields as required
        document.getElementById('studentId').required = true;
        document.getElementById('educationLevel').required = true;
        document.getElementById('gradeLevel').required = true;
        document.getElementById('courseTrack').required = true;
        
        // Remove employee required fields
        document.getElementById('employeeId').required = false;
        document.getElementById('employeeType').required = false;
        document.getElementById('department').required = false;
        
    } else if (patientType === 'Employee') {
        studentFields.style.display = 'none';
        employeeFields.style.display = 'block';
        
        // Remove student required fields
        document.getElementById('studentId').required = false;
        document.getElementById('educationLevel').required = false;
        document.getElementById('gradeLevel').required = false;
        document.getElementById('courseTrack').required = false;
        
        // Set employee fields as required
        document.getElementById('employeeId').required = true;
        document.getElementById('employeeType').required = true;
        document.getElementById('department').required = true;
        
    } else {
        studentFields.style.display = 'none';
        employeeFields.style.display = 'none';
        
        // Remove all required fields
        document.getElementById('studentId').required = false;
        document.getElementById('educationLevel').required = false;
        document.getElementById('gradeLevel').required = false;
        document.getElementById('courseTrack').required = false;
        document.getElementById('employeeId').required = false;
        document.getElementById('employeeType').required = false;
        document.getElementById('department').required = false;
    }
}

function updateGradeLevelOptions() {
    const educationLevel = document.getElementById('educationLevel').value;
    const gradeLevelSelect = document.getElementById('gradeLevel');
    const courseTrackSelect = document.getElementById('courseTrack');
    
    if (!educationLevel) {
        gradeLevelSelect.innerHTML = '<option value="">Select Grade/Year</option>';
        courseTrackSelect.innerHTML = '<option value="">Select Course/Track</option>';
        return;
    }
    
    const data = educationData[educationLevel];
    
    // Update grade levels
    gradeLevelSelect.innerHTML = '<option value="">Select Grade/Year</option>' +
        data.grades.map(grade => `<option value="${grade}">${grade}</option>`).join('');
    
    // Update courses/tracks
    courseTrackSelect.innerHTML = '<option value="">Select Course/Track</option>' +
        data.courses.map(course => `<option value="${course}">${course}</option>`).join('');
    
    // Make fields required
    gradeLevelSelect.required = true;
    courseTrackSelect.required = true;
}

// ========================================
// APPOINTMENTS
// ========================================
function loadAppointments() {
    const yearFilter = document.getElementById('appointmentYearFilter')?.value || '';
    const typeFilter = document.getElementById('appointmentTypeFilter')?.value || '';
    const statusFilter = document.getElementById('appointmentStatusFilter')?.value || '';
    
    const formData = new FormData();
    formData.append('action', 'getAppointments');
    formData.append('year', yearFilter);
    formData.append('patientType', typeFilter);
    formData.append('status', statusFilter);
    
    fetch('./ajax/appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderAppointments(data.appointments);
        }
    })
    .catch(error => console.error('Error:', error));
}

function renderAppointments(appointments) {
    const container = document.getElementById('appointmentsList');
    
    if (!appointments || appointments.length === 0) {
        container.innerHTML = '<p class="text-center">No appointments found</p>';
        return;
    }
    
    container.innerHTML = appointments.map(apt => `
        <div class="appointment-item">
            <div class="appointment-info">
                <h3>${apt.patient_name}</h3>
                <p>
                    <strong>${formatDate(apt.appointment_date)}</strong> at <strong>${formatTime(apt.appointment_time)}</strong>
                    <br>Type: ${apt.appointment_type}
                    ${apt.notes ? `<br>Notes: ${apt.notes}` : ''}
                </p>
            </div>
            <div>
                <span class="badge badge-${apt.status}">${capitalizeFirst(apt.status)}</span>
            </div>
            <div class="action-buttons">
                <button class="btn btn-sm btn-secondary" onclick="editAppointment(${apt.id})">
                    Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="confirmDeleteAppointment(${apt.id}, '${apt.patient_name}')">
                    Delete
                </button>
            </div>
        </div>
    `).join('');
}

function filterAppointments() {
    loadAppointments();
}

function openAddAppointmentModal() {
    document.getElementById('appointmentModalTitle').textContent = 'Add New Appointment';
    document.getElementById('appointmentForm').reset();
    document.getElementById('appointmentId').value = '';
    document.getElementById('appointmentModal').classList.add('active');
    setTimeout(() => updateAppointmentPatientType(), 200);
    loadPatientOptions();
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').classList.remove('active');
}

function editAppointment(id) {
    const formData = new FormData();
    formData.append('action', 'getAppointment');
    formData.append('id', id);

    fetch('./ajax/appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Appointment not found');
            return;
        }

        const apt = data.appointment;

        document.getElementById('appointmentModalTitle').textContent = 'Edit Appointment';
        document.getElementById('appointmentId').value = apt.id;
        document.getElementById('appointmentPatientType').value = apt.patient_type;

        // Load patients first, then assign selected patient
        loadAppointmentPatients(() => {
            document.getElementById('appointmentPatient').value = apt.patient_id;
        });

        document.getElementById('appointmentDate').value = apt.appointment_date;
        document.getElementById('appointmentTime').value = apt.appointment_time;
        document.getElementById('appointmentType').value = apt.appointment_type;
        document.getElementById('appointmentStatus').value = apt.status;
        document.getElementById('appointmentNotes').value = apt.notes || '';

        document.getElementById('appointmentModal').classList.add('active');
    })
    .catch(error => console.error('Error:', error));
}

function confirmDeleteAppointment(id, patientName) {
    deleteType = 'appointment';
    deleteId = id;
    document.getElementById('deleteMessage').textContent = 
        `Are you sure you want to delete the appointment for "${patientName}"? This action cannot be undone.`;
    document.getElementById('deleteModal').classList.add('active');
}

// ========================================
// RECORDS & STATISTICS
// ========================================
function switchRecordsTab(tabId) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.tab-btn').classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    
    if (tabId === 'statistics-tab') {
        loadStatistics();
    }
}

function archiveCompletedAppointments() {
    if (!confirm('This will archive all completed appointments to the records table. Continue?')) {
        return;
    }
    
    showAlert('Archiving completed appointments...', 'info');
    
    const formData = new FormData();
    formData.append('action', 'archiveCompleted');
    
    fetch('ajax/archive.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Successfully archived ${data.archived} appointments!`, 'success');
        } else {
            showAlert(data.message || 'Error archiving appointments', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error archiving appointments', 'danger');
    });
}

// ========================================
// NOTIFICATIONS
// ========================================
function showNotifications() {
    const panel = document.getElementById('notificationPanel');
    panel.classList.add('active');
    loadNotifications();
}

function closeNotifications() {
    document.getElementById('notificationPanel').classList.remove('active');
}

function loadNotifications() {
    const formData = new FormData();
    formData.append('action', 'getNotifications');
    
    fetch('./ajax/appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderNotifications(data.notifications);
        }
    })
    .catch(error => console.error('Error:', error));
}

function renderNotifications(notifications) {
    const container = document.getElementById('notificationList');
    
    if (!notifications || notifications.length === 0) {
        container.innerHTML = '<p class="text-center">No pending notifications</p>';
        return;
    }
    
    container.innerHTML = notifications.map(notif => `
        <div class="notification-item" onclick="goToAppointment(${notif.id})" style="cursor: pointer;">
            <h4>${notif.patient_name}</h4>
            <p>
                Appointment on ${formatDate(notif.appointment_date)} at ${formatTime(notif.appointment_time)}
                <br>Status: <span class="badge badge-${notif.status}">${capitalizeFirst(notif.status)}</span>
            </p>
        </div>
    `).join('');
}

function goToAppointment(appointmentId) {
    closeNotifications();
    navigateTo('appointments');
    // Optional: highlight the appointment
    setTimeout(() => {
        const appointmentItems = document.querySelectorAll('.appointment-item');
        appointmentItems.forEach(item => {
            if (item.innerHTML.includes(`onclick="editAppointment(${appointmentId})`)) {
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                item.style.backgroundColor = '#fef3c7';
                setTimeout(() => {
                    item.style.backgroundColor = '';
                }, 2000);
            }
        });
    }, 300);
}

// ========================================
// FORM SUBMISSIONS
// ========================================
function setupFormHandlers() {
    const patientForm = document.getElementById('patientForm');
    if (patientForm) {
        patientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            savePatient();
        });
    }

    const appointmentForm = document.getElementById('appointmentForm');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveAppointment();
        });
    }
}

function savePatient() {
    const formData = new FormData(document.getElementById('patientForm'));
    formData.append('action', document.getElementById('patientId').value ? 'updatePatient' : 'addPatient');
    
    // Debug: Log what we're sending
    console.log('Saving patient with data:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    fetch('ajax/patients.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data);
        if (data.success) {
            showAlert('Patient saved successfully!', 'success');
            closePatientModal();
            loadPatients();
            loadPatientOptions();
        } else {
            showAlert(data.message || 'Error saving patient', 'danger');
            console.error('Save failed:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving patient: ' + error.message, 'danger');
    });
}

function saveAppointment() {
    const form = document.getElementById('appointmentForm');

    // Ensure patient type is updated before submit
    updateAppointmentPatientType();

    const formData = new FormData(form);
    formData.append('action', document.getElementById('appointmentId').value ? 'updateAppointment' : 'addAppointment');

    // DEBUG LOG — VERY IMPORTANT
    console.log('Submitting appointment:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    fetch('./ajax/appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server response:', data);

        if (data.success) {
            showAlert('Appointment saved successfully!', 'success');
            closeAppointmentModal();
            loadAppointments();
            loadTodayAppointments();
            loadTomorrowAppointments();
        } else {
            showAlert(data.message || 'Missing required fields', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving appointment', 'danger');
    });
}

// ========================================
// DELETE CONFIRMATION
// ========================================
function setupDeleteConfirmation() {
    const confirmInput = document.getElementById('deleteConfirmText');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const errorMsg = document.getElementById('deleteConfirmError');
    
    if (!confirmInput || !confirmBtn || !errorMsg) return;

    confirmInput.addEventListener('input', function()  {
        const value = this.value.trim();
        const correctText = 'First City Providential College';
        
        if (value === correctText) {
            confirmBtn.disabled = false;
            errorMsg.style.display = 'none';
        } else {
            confirmBtn.disabled = true;
            if (value.length > 0) {
                errorMsg.style.display = 'block';
            } else {
                errorMsg.style.display = 'none';
            }
        }
    });
}

function confirmDelete() {
    if (!deleteType || !deleteId) return;
    
    const formData = new FormData();
    formData.append('action', 'delete' + capitalizeFirst(deleteType));
    formData.append('id', deleteId);
    
    if (deleteType === 'patient' && window.deletePatientType) {
        formData.append('type', window.deletePatientType);
    }
    
    const endpoint = deleteType === 'patient' ? 'ajax/patients.php' : 'ajax/appointments.php';
    
    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(capitalizeFirst(deleteType) + ' deleted successfully!', 'success');
            closeDeleteModal();
            
            if (deleteType === 'patient') {
                loadPatients();
                loadPatientOptions();
            } else {
                loadAppointments();
                loadTodayAppointments();
                loadTomorrowAppointments();
            }
        } else {
            showAlert(data.message || 'Error deleting ' + deleteType, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error deleting ' + deleteType, 'danger');
    });
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.getElementById('deleteConfirmText').value = '';
    document.getElementById('deleteConfirmError').style.display = 'none';
    document.getElementById('confirmDeleteBtn').disabled = true;
    deleteType = null;
    deleteId = null;
}

// ========================================
// UTILITY FUNCTIONS
// ========================================
function loadPatientOptions() {
    const formData = new FormData();
    formData.append('action', 'getPatients');
    
    fetch('ajax/patients.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('appointmentPatient');
            select.innerHTML = '<option value="">Select Patient</option>' +
                data.patients.map(p => {
                    let info = p.patient_type;
                    if (p.patient_type === 'Student' && p.education_level) {
                        info += ` - ${p.education_level}`;
                    } else if (p.patient_type === 'Employee' && p.employee_type) {
                        info += ` - ${p.employee_type}`;
                    }
                    return `<option value="${p.id}" data-type="${p.patient_type}">${p.first_name} ${p.last_name} (${info})</option>`;
                }).join('');
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateAppointmentPatientType() {
    const select = document.getElementById('appointmentPatient');
    const selectedOption = select.options[select.selectedIndex];

    if (!selectedOption || !select.value) {
        document.getElementById('appointmentPatientType').value = '';
        return;
    }

    const patientType = selectedOption.dataset.type || selectedOption.getAttribute('data-type');

    document.getElementById('appointmentPatientType').value = patientType || '';
}
function loadYearOptions() {
    const currentYear = new Date().getFullYear();
    const startYear = currentYear - 20;
    
    const yearSelects = [
        'appointmentYearFilter',
        'recordsYearFilter',
    ];
    
    yearSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            let options = '<option value="">All Years</option>';
            for (let year = currentYear; year >= startYear; year--) {
                options += `<option value="${year}">${year}</option>`;
            }
            select.innerHTML = options;
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).replace('-', ' ');
}

function showAlert(message, type = 'info') {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? 'var(--color-success)' : type === 'danger' ? 'var(--color-danger)' : 'var(--color-info)'};
        color: white;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        animation: slideIn 0.3s ease-in-out;
        max-width: 400px;
    `;
    alert.textContent = message;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}
