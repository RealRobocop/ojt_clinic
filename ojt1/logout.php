<?php
/**
 * MediCare - Doctor Logout
 * Clinic Management System
 */

session_start();

// Destroy session
session_destroy();

// Redirect to login
header('Location: login.php');
exit;
?>
