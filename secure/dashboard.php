<?php
// ============================================================================
// dashboard.php — Post-Login Dashboard (SECURE VERSION)
// ============================================================================
// All dynamic output is encoded with htmlspecialchars() to prevent XSS.
// Security headers are set. Session validity is checked.
// ============================================================================
session_start();

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?error=" . urlencode("Please log in first."));
    exit();
}

// Helper function to safely output any value into HTML context
function esc(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — SecureBank™</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #0f0f1a; color: #e0e0e0; }
        .navbar {
            background: #1a1a2e; border-bottom: 1px solid #2a2a4a;
            padding: 16px 30px; display: flex; justify-content: space-between; align-items: center;
        }
        .navbar h1 { font-size: 1.2em; color: #2ec4b6; }
        .navbar a {
            color: #2ec4b6; text-decoration: none; padding: 8px 16px;
            border: 1px solid #2ec4b6; border-radius: 6px; font-size: 0.85em;
        }
        .navbar a:hover { background: #2ec4b6; color: #0f0f1a; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .card {
            background: #1a1a2e; border: 1px solid #2a2a4a;
            border-radius: 12px; padding: 30px; margin-bottom: 20px;
        }
        .card h2 { margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 20px; }
        .info-item { background: #16213e; border-radius: 8px; padding: 20px; }
        .info-item .label { color: #888; font-size: 0.8em; text-transform: uppercase; }
        .info-item .value { font-size: 1.2em; margin-top: 6px; font-weight: 600; }
        .secure-note { background: rgba(46,196,182,0.1); border: 1px solid #2ec4b6; border-radius: 8px; padding: 16px; color: #2ec4b6; font-size: 0.9em; }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🏦 SecureBank™ Dashboard</h1>
        <a href="logout.php">Sign Out</a>
    </nav>
    <div class="container">
        <div class="card">
            <!-- FIX: esc() encodes all output — even if session data contains
                 HTML/JS, it will be rendered as harmless text -->
            <h2>Welcome back, <?php echo esc($_SESSION['username']); ?>!</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">User ID</div>
                    <div class="value">#<?php echo esc((string)$_SESSION['user_id']); ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Role</div>
                    <div class="value"><?php echo esc($_SESSION['role']); ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Login Time</div>
                    <div class="value"><?php echo date('H:i:s'); ?></div>
                </div>
            </div>
        </div>
        <div class="secure-note">
            🛡️ This is the <strong>secure version</strong>. Prepared statements prevent SQL Injection,
            and htmlspecialchars() prevents XSS. Try the same payloads here — they won't work.
        </div>
    </div>
</body>
</html>
