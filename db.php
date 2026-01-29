<?php
/**
 * Database Connection File
 * MediCare Clinic Management System
 * Compatible with XAMPP (MySQL/MariaDB)
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');              // Default XAMPP user
define('DB_PASS', '');                   // Default XAMPP password (empty)
define('DB_NAME', 'clinic_management');

// Create PDO Connection
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

// Close connection (automatically at end of script)
// $pdo = null;
?>
