<?php
// ============================================================================
// authenticate.php — Authentication Handler (VULNERABLE VERSION)
// ============================================================================
//
// THIS FILE IS INTENTIONALLY VULNERABLE TO SQL INJECTION.
//
// The vulnerability exists because user-supplied input ($_POST['username']
// and $_POST['password']) is concatenated directly into a SQL query string
// without any sanitization, escaping, or parameterization.
//
// This means an attacker can break out of the string context in the SQL
// query and inject arbitrary SQL commands.
//
// ─── HOW THE ATTACK WORKS ───
//
// Normal query (user types "admin" / "SuperSecretAdmin!"):
//   SELECT * FROM users WHERE username = 'admin' AND password = 'SuperSecretAdmin!'
//   → Returns 1 row → Login succeeds ✓
//
// Injected query (user types: admin' --  / anything):
//   SELECT * FROM users WHERE username = 'admin' -- ' AND password = 'anything'
//                                                 ^^
//                                     Everything after -- is a SQL comment!
//   → Effectively becomes: SELECT * FROM users WHERE username = 'admin'
//   → Password check is completely bypassed → Login succeeds without password ✓
//
// Even more dangerous (user types: ' OR '1'='1' --  / anything):
//   SELECT * FROM users WHERE username = '' OR '1'='1' -- ' AND password = 'anything'
//   → '1'='1' is always TRUE → Returns ALL rows → First row is admin → Full access ✓
//
// ─── WHY THIS HAPPENS ───
//
// The root cause is TRUST. The code trusts that user input is just data.
// But when you concatenate strings into SQL, the database parser can't
// distinguish between "data" and "instructions". The attacker's input
// becomes part of the query's LOGIC, not just its DATA.
//
// This is the fundamental flaw that Prepared Statements solve — they
// send the query structure and the data through SEPARATE channels,
// making injection impossible by design.
//
// ============================================================================

session_start();
require_once 'config.php';

// --- Disable mysqli exceptions so bad SQL returns false instead of crashing ---
// In PHP 8.1+, mysqli throws exceptions by default on SQL errors.
// We turn that off here because the WHOLE POINT of this demo is to let
// malformed/injected SQL execute (or fail gracefully) rather than crash.
// A real vulnerable app wouldn't crash on injection — it would just... work.
mysqli_report(MYSQLI_REPORT_OFF);

// --- Grab raw user input (no filtering, no escaping — that's the bug) ---
$username = $_POST['username'];
$password = $_POST['password'];

// =========================================================================
// THE VULNERABLE QUERY
// =========================================================================
// String concatenation of user input into SQL = classic injection vector.
//
// The single quotes around $username and $password are the "gates" that
// the attacker breaks through. By injecting a closing quote ('), they
// escape the string literal and enter the SQL command context.
// =========================================================================
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

// --- [DEBUG] Uncomment the next line to see the assembled query ---
// echo "<pre>DEBUG — Executed query:\n" . htmlspecialchars($query) . "</pre>";

$result = mysqli_query($conn, $query);

// --- Check if the query returned any rows ---
if ($result && mysqli_num_rows($result) > 0) {
    // Authentication "succeeded" — fetch the user record
    $user = mysqli_fetch_assoc($result);

    // Store session data (this part is actually fine)
    $_SESSION['logged_in']  = true;
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['username']   = $user['username'];
    $_SESSION['role']       = $user['role'];

    // --- Log the login event (also vulnerable to log injection, but that's
    //     a bonus topic — the attacker could inject fake log entries) ---
    $log_entry = date('Y-m-d H:i:s') . " | LOGIN SUCCESS | User: $username | IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents('auth.log', $log_entry, FILE_APPEND);

    // Redirect to the dashboard
    header("Location: dashboard.php");
    exit();

} else {
    // Authentication failed — redirect back with an error message
    // Note: The error message doesn't reveal whether it was the username
    // or password that was wrong (that part is actually good practice).
    $log_entry = date('Y-m-d H:i:s') . " | LOGIN FAILED  | User: $username | IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents('auth.log', $log_entry, FILE_APPEND);

    header("Location: login.php?error=Invalid credentials. Please try again.");
    exit();
}

// --- Clean up ---
mysqli_close($conn);
?>
