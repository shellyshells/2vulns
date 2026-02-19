<?php
// ============================================================================
// login.php — Login Form (SECURE VERSION)
// ============================================================================
// Key fix: The error parameter is sanitized with htmlspecialchars() before
// being rendered, preventing Reflected XSS attacks.
//
// Additional security headers are also set to harden the page.
// ============================================================================
session_start();

// --- Security headers (defense in depth) ---
header("X-Content-Type-Options: nosniff");          // Prevent MIME sniffing
header("X-Frame-Options: DENY");                     // Prevent clickjacking
header("X-XSS-Protection: 1; mode=block");           // Legacy XSS filter
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'"); // CSP

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank™ — Login Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f0f1a;
            color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: #1a1a2e;
            border: 1px solid #2a2a4a;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo h1 {
            font-size: 1.8em;
            background: linear-gradient(135deg, #2ec4b6, #20a4f3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .logo p { color: #666; font-size: 0.9em; margin-top: 5px; }
        .secure-badge {
            text-align: center;
            margin-bottom: 20px;
        }
        .secure-badge span {
            background: rgba(46,196,182,0.15);
            color: #2ec4b6;
            border: 1px solid #2ec4b6;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; margin-bottom: 6px;
            font-size: 0.85em; color: #aaa;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .form-group input {
            width: 100%; padding: 12px 16px;
            border: 1px solid #2a2a4a; border-radius: 8px;
            background: #16213e; color: #fff; font-size: 1em;
        }
        .form-group input:focus { outline: none; border-color: #2ec4b6; }
        .btn-login {
            width: 100%; padding: 14px; border: none; border-radius: 8px;
            background: linear-gradient(135deg, #2ec4b6, #1a9e8f);
            color: #fff; font-size: 1em; font-weight: 600; cursor: pointer;
        }
        .btn-login:hover { box-shadow: 0 8px 25px rgba(46,196,182,0.4); }
        .error-box {
            background: rgba(233,69,96,0.15); border: 1px solid #e94560;
            border-radius: 8px; padding: 12px; margin-bottom: 20px;
            font-size: 0.9em; color: #ff6b6b;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🏦 SecureBank™</h1>
            <p>Employee Portal v3.0.0 — Hardened</p>
        </div>
        <div class="secure-badge">
            <span>🛡️ Secure Version</span>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-box">
                <?php
                // =============================================================
                // FIX: htmlspecialchars() converts special HTML characters into
                // harmless entities:
                //   <  →  &lt;
                //   >  →  &gt;
                //   "  →  &quot;
                //   '  →  &#039;
                //
                // So <script>alert('XSS')</script> becomes:
                //   &lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;
                //
                // The browser renders it as visible TEXT, not executable code.
                // =============================================================
                echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
                ?>
            </div>
        <?php endif; ?>

        <form action="authenticate.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Enter your username" required autofocus
                       maxlength="50"> <!-- Client-side length limit -->
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required
                       maxlength="255">
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>
</body>
</html>
