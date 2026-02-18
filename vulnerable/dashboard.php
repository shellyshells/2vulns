<?php
// ============================================================================
// dashboard.php — Post-Login Dashboard (VULNERABLE VERSION)
// ============================================================================
// This page is shown after successful authentication.
// It displays the logged-in user's information.
//
// VULNERABILITY: The username stored in $_SESSION was originally taken
// from raw user input (via the SQLi in authenticate.php). If the attacker
// injected something like: <script>alert('XSS')</script>' --
// ...that string might end up in the session and be reflected here
// without encoding — a second-order injection / stored XSS scenario.
// ============================================================================

session_start();

// --- Access control: redirect to login if not authenticated ---
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?error=Please log in first.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — SecureBank™</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f0f1a;
            color: #e0e0e0;
            min-height: 100vh;
        }
        .navbar {
            background: #1a1a2e;
            border-bottom: 1px solid #2a2a4a;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            font-size: 1.2em;
            background: linear-gradient(135deg, #e94560, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .navbar a {
            color: #e94560;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #e94560;
            border-radius: 6px;
            font-size: 0.85em;
            transition: all 0.3s;
        }
        .navbar a:hover { background: #e94560; color: #fff; }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .welcome-card {
            background: #1a1a2e;
            border: 1px solid #2a2a4a;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
        }
        .welcome-card h2 { font-size: 1.5em; margin-bottom: 10px; }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .badge-admin { background: rgba(233,69,96,0.2); color: #e94560; border: 1px solid #e94560; }
        .badge-user  { background: rgba(46,196,182,0.2); color: #2ec4b6; border: 1px solid #2ec4b6; }
        .badge-mod   { background: rgba(255,183,77,0.2); color: #ffb74d; border: 1px solid #ffb74d; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }
        .info-item {
            background: #16213e;
            border-radius: 8px;
            padding: 20px;
        }
        .info-item .label { color: #888; font-size: 0.8em; text-transform: uppercase; letter-spacing: 1px; }
        .info-item .value { font-size: 1.2em; margin-top: 6px; font-weight: 600; }
        .alert-box {
            background: rgba(255,183,77,0.1);
            border: 1px solid #ffb74d;
            border-radius: 8px;
            padding: 16px;
            margin-top: 20px;
            font-size: 0.9em;
            color: #ffb74d;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🏦 SecureBank™ Dashboard</h1>
        <a href="logout.php">Sign Out</a>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>
                Welcome back,
                <?php
                // =============================================================
                // VULNERABILITY: Echoing session data without encoding.
                // If the username in the DB (or injected via SQLi) contains
                // HTML/JS, it will execute here.
                // FIX: echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
                // =============================================================
                echo $_SESSION['username'];
                ?>!
            </h2>

            <?php
            // Display a role badge with color coding
            $role = $_SESSION['role'];
            $badge_class = 'badge-user';
            if ($role === 'administrator') $badge_class = 'badge-admin';
            if ($role === 'moderator')     $badge_class = 'badge-mod';
            ?>
            <span class="badge <?php echo $badge_class; ?>">
                <?php echo $role; ?>
            </span>

            <div class="info-grid">
                <div class="info-item">
                    <div class="label">User ID</div>
                    <div class="value">#<?php echo $_SESSION['user_id']; ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Session ID</div>
                    <div class="value" style="font-size:0.7em; word-break:break-all;">
                        <?php echo session_id(); ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="label">Login Time</div>
                    <div class="value"><?php echo date('H:i:s'); ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Your IP</div>
                    <div class="value"><?php echo $_SERVER['REMOTE_ADDR']; ?></div>
                </div>
            </div>
        </div>

        <?php if ($role === 'administrator'): ?>
        <div class="alert-box">
            ⚠️ You are logged in as <strong>administrator</strong>.
            If you reached this page via SQL Injection, congratulations — you just
            bypassed authentication without knowing the password.
            Think about what an attacker could do from here.
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
