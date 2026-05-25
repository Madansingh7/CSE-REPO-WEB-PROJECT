-- ============================================================
--  CSE Project Repository - Database Setup Script
--  Run this in phpMyAdmin or MySQL command line
-- ============================================================

-- Step 1: Create the database
CREATE DATABASE IF NOT EXISTS cse_repository;

-- Step 2: Select the database
USE cse_repository;

-- Step 3: Create the projects table
CREATE TABLE IF NOT EXISTS projects (
    id          INT AUTO_INCREMENT PRIMARY KEY,   -- Unique ID for each project
    title       VARCHAR(255)  NOT NULL,           -- Project title
    student_name VARCHAR(100) NOT NULL,           -- Name of student
    year        INT           NOT NULL,           -- Year of project (e.g. 2024)
    category    VARCHAR(100)  NOT NULL,           -- Category (Web Dev, AI, etc.)
    technology  VARCHAR(255)  NOT NULL,           -- Technologies used
    description TEXT          NOT NULL,           -- Full description
    github_link VARCHAR(255)  DEFAULT '',         -- GitHub URL (optional)
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP  -- Auto timestamp
);

-- Step 4: Insert sample data so the site is not empty on first run
INSERT INTO projects (title, student_name, year, category, technology, description, github_link) VALUES
(
    'Online Voting System',
    'Rahul Sharma',
    2024,
    'Web Development',
    'HTML, CSS, PHP, MySQL',
    'A secure online voting system built for college elections. It includes an admin panel to manage candidates and a voter interface with unique login credentials.',
    'https://github.com/rahul/online-voting'
),
(
    'Student Result Management',
    'Priya Patel',
    2024,
    'Database Management',
    'PHP, MySQL, Bootstrap, JavaScript',
    'A complete result management system that allows teachers to enter marks and students to view their results with grade calculation and GPA tracking.',
    'https://github.com/priya/result-management'
),
(
    'Library Management System',
    'Amit Kumar',
    2023,
    'Web Development',
    'HTML, CSS, JavaScript, PHP, MySQL',
    'Track book inventory, manage member registrations, and handle book issue and return records with due date reminders and fine calculation.',
    'https://github.com/amit/library-system'
),
(
    'Face Recognition Attendance',
    'Sneha Reddy',
    2024,
    'Artificial Intelligence',
    'Python, OpenCV, MySQL, Tkinter',
    'Automated attendance system using face recognition. The system captures student faces via webcam and marks attendance automatically in the database.',
    'https://github.com/sneha/face-attendance'
),
(
    'Hospital Appointment Booking',
    'Karan Mehta',
    2023,
    'Web Development',
    'HTML, CSS, PHP, MySQL, JavaScript',
    'Patients can register, search for doctors by specialization, book appointments, and view appointment history. Doctors can manage their schedule.',
    'https://github.com/karan/hospital-booking'
);

-- ============================================================
--  DONE! Your database is ready.
--  Now go to: http://localhost/cse_repository/
-- ============================================================
