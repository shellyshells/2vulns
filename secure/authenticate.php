<?php
// ============================================================================
// authenticate.php — Authentication Handler (SECURE VERSION)
// ============================================================================
//
// This file demonstrates the CORRECT way to handle user authentication.
// Every vulnerability from the vulnerable version is addressed here.
//
// ─── DEFENSE LAYERS IMPLEMENTED ───
//
// 1. PREPARED STATEMENTS (Primary defense against SQL Injection)
//    Instead of concatenating user input into the query string, we use
//    placeholders (?). The database engine receives the query structure
//    and the data through SEPARATE channels. This makes it structurally
//    impossible for user input to alter the query's logic.
//
//    Vulnerable:  "SELECT * FROM users WHERE username = '$input'"
//                 → Input IS the query → Injection possible
//
//    Secure:      "SELECT * FROM users WHERE username = ?"
//                 → Input is bound as DATA → Injection impossible
//
// 2. INPUT VALIDATION (Defense in depth)
//    Even though prepared statements prevent SQLi, we still validate
//    input length and format. Why? Because defense in depth means
//    not relying on a single control.
//
// 3. PASSWORD HASHING (Bonus — not in vulnerable version)
//    In production, passwords should be hashed with password_hash()
//    and verified with password_verify(). We show a commented example.
//
// 4. RATE LIMITING (Mentioned, not fully implemented)
//    A real app should track failed login attempts and lock accounts
//    or add delays after N failures.
//
// 5. SECURE SESSION HANDLING
//    session_regenerate_id() prevents session fixation attacks.
//
// ============================================================================

session_start();
require_once 'config.php';

// --- Input validation: reject obviously bad input early ---
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Basic sanity checks
if (empty($username) || empty($password)) {
    header("Location: login.php?error=" . urlencode("All fields are required."));
    exit();
}

// Length limits — no legitimate username is 500 characters long
if (strlen($username) > 50 || strlen($password) > 255) {
    header("Location: login.php?error=" . urlencode("Invalid input length."));
    exit();
}

// =========================================================================
// THE SECURE QUERY — Prepared Statement with Parameter Binding
// =========================================================================
//
// Step 1: prepare() — Send the query TEMPLATE to the database.
//         The "?" marks are placeholders, NOT string concatenation.
//         The DB engine compiles the query plan HERE, before seeing any data.
//
// Step 2: bind_param() — Send the user's data separately.
//         "ss" means two string parameters. The DB knows these are DATA,
//         not SQL commands. Even if the user types: admin' --
//         the database treats it as the literal string "admin' --"
//         and searches for a username that literally contains a quote and dashes.
//         It won't find one. Injection defeated.
//
// Step 3: execute() — Run the pre-compiled query with the bound data.
//
// This is called "parameterized queries" or "prepared statements" and is
// the #1 defense against SQL Injection recommended by OWASP.
// =========================================================================

$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND password = ?");

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die("A system error occurred.");
}

// "ss" = two String parameters. Types: s=string, i=integer, d=double, b=blob
$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    // Exactly one matching user — authentication successful
    $user = $result->fetch_assoc();

    // SECURITY: Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['logged_in']  = true;
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['username']   = $user['username']; // From DB, not from user input
    $_SESSION['role']       = $user['role'];

    // Secure logging — no user input in log (prevents log injection)
    error_log("LOGIN SUCCESS | UserID: " . $user['id'] . " | IP: " . $_SERVER['REMOTE_ADDR']);

    header("Location: dashboard.php");
    exit();

} else {
    // Authentication failed
    error_log("LOGIN FAILED | Attempted user: " . substr($username, 0, 20) . " | IP: " . $_SERVER['REMOTE_ADDR']);

    // Generic error — don't reveal whether it was username or password
    header("Location: login.php?error=" . urlencode("Invalid credentials. Please try again."));
    exit();
}

// --- Clean up ---
$stmt->close();
$conn->close();

// =========================================================================
// BONUS: How this would look with password hashing (production-ready)
// =========================================================================
//
// During user registration:
//   $hashed = password_hash($raw_password, PASSWORD_BCRYPT, ['cost' => 12]);
//   // Store $hashed in the database
//
// During login (query by username only, verify hash in PHP):
//   $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
//   $stmt->bind_param("s", $username);
//   $stmt->execute();
//   $user = $stmt->get_result()->fetch_assoc();
//
//   if ($user && password_verify($raw_password, $user['password'])) {
//       // Login success
//   }
//
// This way, even if the database is stolen, the attacker only gets hashes.
// =========================================================================
?>
