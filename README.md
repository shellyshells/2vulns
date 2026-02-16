# 🏦 SecureBank™ — SQL Injection Lab

> A deliberately vulnerable web application demonstrating SQL Injection and its remediation.  
> Built for educational purposes in a controlled pentest lab environment.

---

## 📂 Project Structure

```
projet_tp2/
├── database.sql              # Database schema + seed data
├── README.md                 # You're reading it
├── vulnerable/               # ⚠️  INTENTIONALLY VULNERABLE version
│   ├── config.php            #     DB connection (root, no password)
│   ├── login.php             #     Login form (Reflected XSS via ?error=)
│   ├── authenticate.php      #     Auth handler (SQL Injection via concatenation)
│   ├── dashboard.php         #     Post-login page (session data reflected raw)
│   └── logout.php            #     Session destruction
└── secure/                   # 🛡️  PATCHED version
    ├── config.php            #     DB connection (low-privilege user)
    ├── login.php             #     Login form (htmlspecialchars on error)
    ├── authenticate.php      #     Auth handler (Prepared Statements)
    ├── dashboard.php         #     Post-login page (all output encoded)
    └── logout.php            #     Session destruction
```
## 🔑 Test Credentials

| Username | Password | Role |
|----------|----------|------|
| admin | SuperSecretAdmin! | administrator |
| alice | alice2024 | user |
| bob | bobpassword | user |
| charlie | charlie123 | moderator |
| eve | eveisdropping | user |

---

## 🚀 Setup Guide

### Prerequisites

| Software | Version | Purpose |
|----------|---------|---------|
| PHP | 7.4+ (8.x recommended) | Server-side runtime |
| MySQL | 5.7+ or MariaDB 10.3+ | Database |
| A web browser | Any modern browser | Testing the app |

The easiest way is to install **XAMPP** (Windows/Mac/Linux), which bundles Apache + PHP + MySQL together.

### Step-by-Step Installation

#### 1. Install XAMPP
Download from [https://www.apachefriends.org](https://www.apachefriends.org) and install.

#### 2. Start Services
Open the XAMPP Control Panel and start **Apache** and **MySQL**.

#### 3. Deploy the Files
Copy the entire `projet_tp2/` folder into your XAMPP web root:
- **Windows**: `C:\xampp\htdocs\projet_tp2\`
- **macOS**: `/Applications/XAMPP/htdocs/projet_tp2/`
- **Linux**: `/opt/lampp/htdocs/projet_tp2/`

#### 4. Set Up the Database
Open phpMyAdmin at `http://localhost/phpmyadmin`, then:
1. Click the **SQL** tab
2. Paste the contents of `database.sql`
3. Click **Go**

Or via command line:
```bash
mysql -u root < /path/to/projet_tp2/database.sql
```

#### 5. Access the Application
- **Vulnerable version**: [http://localhost/projet_tp2/vulnerable/login.php](http://localhost/projet_tp2/vulnerable/login.php)
- **Secure version**: [http://localhost/projet_tp2/secure/login.php](http://localhost/projet_tp2/secure/login.php)

---

## 📝 Technical Summary

| Aspect | Vulnerable Version | Secure Version |
|--------|-------------------|----------------|
| SQL Query | String concatenation | Prepared statements |
| Error Display | Raw `$_GET` output | `htmlspecialchars()` |
| DB User | root (full privileges) | labuser (limited) |
| Error Messages | Reveals DB internals | Generic messages |
| Session | No regeneration | `session_regenerate_id()` |
| Headers | None | CSP, X-Frame-Options, etc. |
| Input Validation | None | Length + emptiness checks |


## 📚 References

- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Scripting_Prevention_Cheat_Sheet.html)
- [PHP Prepared Statements](https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php)
- [PortSwigger SQL Injection Labs](https://portswigger.net/web-security/sql-injection)
