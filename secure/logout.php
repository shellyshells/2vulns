<?php
// ============================================================================
// logout.php — Session Termination
// ============================================================================
// Destroys the session and redirects to the login page.
// This part is actually implemented correctly — even vulnerable apps
// can get some things right!
// ============================================================================

session_start();

// Wipe all session variables
$_SESSION = array();

// Delete the session cookie (belt and suspenders)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session on the server side
session_destroy();

// Send the user back to login
header("Location: login.php?error=You have been logged out.");
exit();
?>
