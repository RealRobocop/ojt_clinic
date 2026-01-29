/**
 * MediCare - Clinic Management System
 * Frontend JavaScript with AJAX/PHP Integration
 * Compatible with XAMPP
 */

// API Base URL
const API_BASE = './api';

// ========================================
// SECTION NAVIGATION
// ========================================
function navigateTo(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active from nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Show selected section
    const section = document.getElementById(sectionId);
    if (section) {
        section.classList.add('active');
    }
    
    // Add active to nav button
    const navItem = document.querySelector(`[data-section="${sectionId}"]`);
    if (navItem) {
        navItem.classList.add('active');
    }
    
    // Load data based on section
    if (sectionId === 'patients') {
        loadPatients();
    } else if (sectionId === 'appointments') {
        loadAppointments();
    }
    
    // Close sidebar on mobile
    if (window.innerWidth <= 768) {
        closeSidebar();
    }
}

// ========================================
// LOGOUT FUNCTION
// ========================================
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}

// ========================================
// SIDEBAR TOGGLE
// ========================================
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
}

document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            closeSidebar();
        }
    }
});

function toggleSidebar() {
    sidebar.classList.toggle('mobile-open');
}

function closeSidebar() {
    sidebar.classList.remove('mobile-open');
}

// ========================================
// PATIENT MANAGEMENT
// ========================================

async function loadPatients() {
    try {
        const response = await fetch(`${API_BASE}/patients.php/list`);
        const result = await response.json();
        
        if (result.success) {
            displayPatients(result.data);
        } else {
            showNotification('Failed to load patients', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error loading patients', 'error');
    }
}

function displayPatients(patients) {
    const tbody = document.getElementById('patientsList');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (patients.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No patients found</td></tr>';
        return;
    }
    
    patients.forEach(patient => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="patient-name">
                    <div class="patient-avatar">${patient.name.substring(0, 2).toUpperCase()}</div>
                    ${escapeHtml(patient.name)}
                </div>
            </td>
            <td>${patient.age}</td>
            <td>${escapeHtml(patient.phone || 'N/A')}</td>
            <td>${escapeHtml(patient.email || 'N/A')}</td>
            <td>${patient.dateAdded}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-table" title="View" onclick="viewPatient(${patient.id})">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                    <button class="btn-table" title="Edit" onclick="editPatient(${patient.id})">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                        </svg>
                    </button>
                    <button class="btn-table danger" title="Delete" onclick="deletePatient(${patient.id})">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function openAddPatientModal() {
    document.getElementById('patientId').value = '';
    document.getElementById('patientModalTitle').textContent = 'Add New Patient';
    document.getElementById('patientForm').reset();
    document.getElementById('patientModal').classList.add('active');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.remove('active');
}

async function savePatient(event) {
    event.preventDefault();
    
    const id = document.getElementById('patientId').value;
    const patientData = {
        name: document.getElementById('patientName').value,
        age: document.getElementById('patientAge').value,
        gender: document.getElementById('patientGender').value,
        phone: document.getElementById('patientPhone').value,
        email: document.getElementById('patientEmail').value,
        address: document.getElementById('patientAddress').value
    };
    
    try {
        let response;
        if (id) {
            // Update
            response = await fetch(`${API_BASE}/patients.php/update/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(patientData)
            });
        } else {
            // Create
            response = await fetch(`${API_BASE}/patients.php/create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(patientData)
            });
        }
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(id ? 'Patient updated successfully!' : 'Patient created successfully!', 'success');
            closePatientModal();
            loadPatients();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error saving patient', 'error');
    }
}

async function viewPatient(id) {
    try {
        const response = await fetch(`${API_BASE}/patients.php/get/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const p = result.data;
            alert(`Patient: ${p.name}\nAge: ${p.age}\nGender: ${p.gender}\nPhone: ${p.phone || 'N/A'}\nEmail: ${p.email || 'N/A'}\nAddress: ${p.address || 'N/A'}`);
        } else {
            showNotification('Patient not found', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error viewing patient', 'error');
    }
}

async function editPatient(id) {
    try {
        const response = await fetch(`${API_BASE}/patients.php/get/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const p = result.data;
            document.getElementById('patientId').value = p.id;
            document.getElementById('patientName').value = p.name;
            document.getElementById('patientAge').value = p.age;
            document.getElementById('patientGender').value = p.gender;
            document.getElementById('patientPhone').value = p.phone || '';
            document.getElementById('patientEmail').value = p.email || '';
            document.getElementById('patientAddress').value = p.address || '';
            
            document.getElementById('patientModalTitle').textContent = 'Edit Patient';
            document.getElementById('patientModal').classList.add('active');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error loading patient', 'error');
    }
}

function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient? This action cannot be undone.')) {
        deletePatientConfirmed(id);
    }
}

async function deletePatientConfirmed(id) {
    try {
        const response = await fetch(`${API_BASE}/patients.php/delete/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Patient deleted successfully!', 'success');
            loadPatients();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error deleting patient', 'error');
    }
}

function searchPatients() {
    const query = document.getElementById('searchInput').value;
    if (query.length < 2) {
        loadPatients();
        return;
    }
    
    fetch(`${API_BASE}/patients.php/search?q=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(result => {
            if (result.success) {
                displayPatients(result.data);
            }
        })
        .catch(err => console.error('Error:', err));
}

// ========================================
// APPOINTMENT MANAGEMENT
// ========================================

async function loadAppointments() {
    try {
        const response = await fetch(`${API_BASE}/appointments.php/upcoming`);
        const result = await response.json();
        
        if (result.success) {
            displayAppointments(result.data);
            loadPatientDropdown();
        } else {
            showNotification('Failed to load appointments', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error loading appointments', 'error');
    }
}

function displayAppointments(appointments) {
    const container = document.getElementById('appointmentsList');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (appointments.length === 0) {
        container.innerHTML = '<p style="text-align: center; padding: 2rem;">No upcoming appointments</p>';
        return;
    }
    
    appointments.forEach(appt => {
        const div = document.createElement('div');
        div.className = 'appointment-item';
        div.innerHTML = `
            <div class="appointment-time">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                ${appt.appointmentTime}
            </div>
            <div class="appointment-info">
                <h4>${escapeHtml(appt.patientName)}</h4>
                <p>${escapeHtml(appt.appointmentType)}</p>
            </div>
            <div class="appointment-status ${appt.status}">${appt.status}</div>
            <div class="action-buttons">
                <button class="btn-table" title="Edit" onclick="editAppointment(${appt.id})">
                    <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                    </svg>
                </button>
                <button class="btn-table danger" title="Delete" onclick="deleteAppointment(${appt.id})">
                    <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(div);
    });
}

async function loadPatientDropdown() {
    try {
        const response = await fetch(`${API_BASE}/patients.php/list`);
        const result = await response.json();
        
        if (result.success) {
            const select = document.getElementById('appointmentPatient');
            const currentValue = select.value;
            
            // Clear and rebuild
            select.innerHTML = '<option value="">Select Patient</option>';
            result.data.forEach(patient => {
                const option = document.createElement('option');
                option.value = patient.id;
                option.textContent = patient.name;
                select.appendChild(option);
            });
            
            if (currentValue) select.value = currentValue;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function openAddAppointmentModal() {
    document.getElementById('appointmentId').value = '';
    document.getElementById('appointmentModalTitle').textContent = 'Add New Appointment';
    document.getElementById('appointmentForm').reset();
    loadPatientDropdown();
    document.getElementById('appointmentModal').classList.add('active');
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').classList.remove('active');
}

async function saveAppointment(event) {
    event.preventDefault();
    
    const id = document.getElementById('appointmentId').value;
    const patientId = document.getElementById('appointmentPatient').value;
    const patientName = document.querySelector(`#appointmentPatient option[value="${patientId}"]`).textContent;
    
    const appointmentData = {
        patientId: patientId,
        patientName: patientName,
        appointmentDate: document.getElementById('appointmentDate').value,
        appointmentTime: document.getElementById('appointmentTime').value,
        appointmentType: document.getElementById('appointmentType').value,
        status: document.getElementById('appointmentStatus').value,
        notes: document.getElementById('appointmentNotes').value
    };
    
    try {
        let response;
        if (id) {
            response = await fetch(`${API_BASE}/appointments.php/update/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(appointmentData)
            });
        } else {
            response = await fetch(`${API_BASE}/appointments.php/create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(appointmentData)
            });
        }
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(id ? 'Appointment updated!' : 'Appointment created!', 'success');
            closeAppointmentModal();
            loadAppointments();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error saving appointment', 'error');
    }
}

async function editAppointment(id) {
    try {
        const response = await fetch(`${API_BASE}/appointments.php/get/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const a = result.data;
            document.getElementById('appointmentId').value = a.id;
            document.getElementById('appointmentPatient').value = a.patientId;
            document.getElementById('appointmentDate').value = a.appointmentDate;
            document.getElementById('appointmentTime').value = a.appointmentTime;
            document.getElementById('appointmentType').value = a.appointmentType;
            document.getElementById('appointmentStatus').value = a.status;
            document.getElementById('appointmentNotes').value = a.notes || '';
            
            loadPatientDropdown().then(() => {
                document.getElementById('appointmentPatient').value = a.patientId;
                document.getElementById('appointmentModalTitle').textContent = 'Edit Appointment';
                document.getElementById('appointmentModal').classList.add('active');
            });
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error loading appointment', 'error');
    }
}

function deleteAppointment(id) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        deleteAppointmentConfirmed(id);
    }
}

async function deleteAppointmentConfirmed(id) {
    try {
        const response = await fetch(`${API_BASE}/appointments.php/delete/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Appointment deleted!', 'success');
            loadAppointments();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error deleting appointment', 'error');
    }
}

// ========================================
// UTILITY FUNCTIONS
// ========================================

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showUserMenu() {
    alert('Logged in as: ' + document.querySelector('.user-name').textContent);
}

// ========================================
// INITIALIZATION
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('MediCare XAMPP/PHP System Initialized');
    loadPatients();
    loadAppointments();
});

// Handle responsive
let currentWidth = window.innerWidth;
window.addEventListener('resize', () => {
    const newWidth = window.innerWidth;
    if ((currentWidth > 768 && newWidth <= 768) || (currentWidth <= 768 && newWidth > 768)) {
        closeSidebar();
    }
    currentWidth = newWidth;
});