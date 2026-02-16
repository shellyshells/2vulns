<?php
// ============================================================================
// config.php — Database Configuration (VULNERABLE VERSION)
// ============================================================================
// This file centralizes database connection parameters.
// In a real application, this file should:
//   1. Live OUTSIDE the web root (not accessible via URL)
//   2. Use environment variables instead of hardcoded credentials
//   3. Never be committed to version control
//
// Here we use root for simplicity — the secure version uses 'labuser'.
// ============================================================================

// --- Database connection parameters ---
define('DB_HOST',     'localhost');
define('DB_USER',     'labuser');        // VULNERABILITY: In a real scenario this would be root
define('DB_PASS',     'labpass123');     // VULNERABILITY: Hardcoded credentials in source code
define('DB_NAME',     'pentest_lab');
define('DB_CHARSET',  'utf8mb4');

// --- Establish the connection ---
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Bail out if the connection failed ---
if (!$conn) {
    // VULNERABILITY: Leaking internal error details to the user.
    // An attacker can learn the DB type, version, and host from this.
    die("Connection failed: " . mysqli_connect_error());
}

// Set the character set (at least we got this right)
mysqli_set_charset($conn, DB_CHARSET);
?>
