<?php
// ============================================================================
// config.php — Database Configuration (SECURE VERSION)
// ============================================================================
//
// KEY DIFFERENCES FROM THE VULNERABLE VERSION:
//   1. Uses a dedicated low-privilege database user ('labuser'), not 'root'.
//   2. Error messages are generic — no internal details leaked to the user.
//   3. Uses mysqli in OOP mode for cleaner prepared statement support.
//   4. Sets strict SQL mode to catch silent data truncation attacks.
//
// In production, you'd also:
//   - Store credentials in environment variables (getenv('DB_USER'))
//   - Place this file outside the web root
//   - Use a .env file parsed by a library like vlucas/phpdotenv
// ============================================================================

define('DB_HOST',    'localhost');
define('DB_USER',    'labuser');         // Low-privilege user (SELECT, INSERT, UPDATE only)
define('DB_PASS',    'labpass123');       // Not root, not empty
define('DB_NAME',    'pentest_lab');
define('DB_CHARSET', 'utf8mb4');

// --- Create the connection using OOP style (better for prepared statements) ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    // SECURE: Log the real error internally, show the user nothing useful
    error_log("Database connection failed: " . $conn->connect_error);
    die("A system error occurred. Please try again later.");
}

// Set character encoding
$conn->set_charset(DB_CHARSET);

// Enable strict mode — prevents silent truncation attacks
// (e.g., 'admin           x' being silently truncated to 'admin')
$conn->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
?>
