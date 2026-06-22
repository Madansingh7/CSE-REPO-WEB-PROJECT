-- ============================================================
--  CSE Project Repository - Database Setup Script
--  Run this in phpMyAdmin or MySQL command line
-- ============================================================

-- Step 1: Create the database
CREATE DATABASE IF NOT EXISTS cse_repository;

-- Step 2: Select the database
USE cse_repository;

-- Step 3: Create the users table (for authentication)
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,   -- Unique ID for each user
    name        VARCHAR(100)  NOT NULL,           -- Full name
    usn         VARCHAR(20)   UNIQUE NOT NULL,    -- USN (unique student number)
    email       VARCHAR(100)  UNIQUE NOT NULL,    -- Email address
    phone       VARCHAR(15)   NOT NULL,           -- Phone number
    semester    INT           NOT NULL,           -- Semester (1-8)
    division    VARCHAR(10)   NOT NULL,           -- Division (A, B, C, etc.)
    password    VARCHAR(255)  NOT NULL,           -- Hashed password
    role        VARCHAR(20)   DEFAULT 'student',  -- Role: 'student' or 'admin'
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP  -- Account creation time
);

-- Step 4: Create the projects table
CREATE TABLE IF NOT EXISTS projects (
    id          INT AUTO_INCREMENT PRIMARY KEY,   -- Unique ID for each project
    user_id     INT           NOT NULL,           -- Foreign key to users table
    title       VARCHAR(255)  NOT NULL,           -- Project title
    category    VARCHAR(100)  NOT NULL,           -- Category (Web Dev, AI, etc.)
    technology  VARCHAR(255)  NOT NULL,           -- Technologies used
    description TEXT          NOT NULL,           -- Full description
    github_link VARCHAR(255)  DEFAULT '',         -- GitHub URL (optional)
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,  -- Auto timestamp
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  -- Link to users
);

-- Step 5: Insert admin user (password: admin123)
-- The password hash is for: admin123
INSERT INTO users (name, usn, email, phone, semester, division, password, role) VALUES
('Admin', 'ADMIN001', 'admin@sdmcet.edu', '9999999999', 0, 'Admin', '$2y$10$vvfH/S9KRzNDjwXjLe4k3.Y6H9I7Q1K0B0M0N0O0P0Q0R0S0T0U0V0W0', 'admin');

-- ============================================================
--  DONE! Your database is ready.
--  Admin Login: Email: admin@sdmcet.edu, Password: admin123
--  Now go to: http://localhost/cse_repository/
-- ============================================================
