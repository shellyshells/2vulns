-- ============================================================================
-- DATABASE SETUP SCRIPT — SQL Injection Lab
-- ============================================================================
-- This script initializes the MySQL database used by both the VULNERABLE
-- and SECURE versions of the login application.
--
-- IMPORTANT: This is an educational environment. The passwords below are
-- stored in PLAINTEXT on purpose — in production you'd use bcrypt/argon2.
-- That deliberate weakness is part of the demonstration.
-- ============================================================================

-- Step 1: Create a dedicated database (isolated from any real data)
CREATE DATABASE IF NOT EXISTS pentest_lab
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pentest_lab;

-- Step 2: Drop the table if it exists (idempotent setup)
DROP TABLE IF EXISTS users;

-- Step 3: Build the users table
-- Notice: passwords are VARCHAR(255) — large enough for hashed passwords
-- in the secure version, but here we store them as plaintext to keep
-- the vulnerable demo simple and observable.
CREATE TABLE users (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        VARCHAR(20)  NOT NULL DEFAULT 'user',
    email       VARCHAR(100),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Step 4: Seed some test accounts
-- These credentials are intentionally weak — that's the point.
INSERT INTO users (username, password, role, email) VALUES
    ('admin',       'SuperSecretAdmin!',    'administrator',  'admin@lab.local'),
    ('alice',       'alice2024',            'user',           'alice@lab.local'),
    ('bob',         'bobpassword',          'user',           'bob@lab.local'),
    ('charlie',     'charlie123',           'moderator',      'charlie@lab.local'),
    ('eve',         'eveisdropping',        'user',           'eve@lab.local');

-- ============================================================================
-- VERIFICATION QUERY (run manually to confirm setup)
-- SELECT id, username, role FROM users;
-- Expected: 5 rows — admin, alice, bob, charlie, eve
-- ============================================================================

-- Step 5: Create a dedicated MySQL user with LIMITED privileges
-- In a real scenario, the app user should NEVER be root.
-- This is the "principle of least privilege" in action.
CREATE USER IF NOT EXISTS 'labuser'@'localhost' IDENTIFIED BY 'labpass123';
GRANT SELECT, INSERT, UPDATE ON pentest_lab.* TO 'labuser'@'localhost';
FLUSH PRIVILEGES;

-- NOTE: We intentionally do NOT grant DELETE or DROP privileges.
-- Even if someone injects `DROP TABLE users`, it will fail because
-- the DB user lacks permission. Defense in depth!
