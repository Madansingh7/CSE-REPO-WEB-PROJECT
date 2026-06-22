<?php
// ============================================================
//  auth.php - Authentication Helper Functions
//  Provides functions for login, signup, logout, and session management
// ============================================================

// Start PHP session (required for session variables)
session_start();

// --- Function: Check if user is logged in ---
// Returns true if session user_id exists, false otherwise
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// --- Function: Get current logged-in user's ID ---
// Returns the user ID or 0 if not logged in
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? 0;
}

// --- Function: Get current logged-in user's name ---
// Returns the user name or empty string if not logged in
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? '';
}

// --- Function: Get current logged-in user's role ---
// Returns 'student', 'admin', or empty string if not logged in
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? '';
}

// --- Function: Check if current user is admin ---
// Returns true if user is admin, false otherwise
function isAdmin() {
    return getCurrentUserRole() === 'admin';
}

// --- Function: Require login (redirect if not logged in) ---
// Call this at the start of pages that require authentication
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// --- Function: Require admin access (redirect if not admin) ---
// Call this at the start of admin-only pages
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// --- Function: Validate password strength ---
// Returns empty string if valid, or error message if invalid
function validatePassword($password) {
    if (strlen($password) < 6) {
        return "Password must be at least 6 characters long.";
    }
    return "";
}

// --- Function: Validate USN format ---
// Expected format: 2sd24cs034 (year + college code + branch + roll number)
function validateUSN($usn) {
    // Basic USN validation pattern
    if (!preg_match('/^[0-9]{1}[a-z]{2}[0-9]{2}[a-z]{2}[0-9]{3}$/i', $usn)) {
        return "USN format invalid. Example: 2sd24cs034";
    }
    return "";
}

// --- Function: Validate email format ---
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email address.";
    }
    return "";
}

// --- Function: Validate phone number ---
function validatePhone($phone) {
    if (!preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        return "Please enter a valid 10-digit phone number.";
    }
    return "";
}

// --- Function: Validate semester ---
function validateSemester($semester) {
    $sem = (int)$semester;
    if ($sem < 1 || $sem > 8) {
        return "Semester must be between 1 and 8.";
    }
    return "";
}

// --- Function: Redirect to login if not logged in ---
// Used after logout or when access is denied
function redirectToLogin($message = '') {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: login.php");
    exit();
}

// --- Function: Redirect to dashboard if already logged in ---
// Used on login/signup pages to prevent logged-in users from accessing them
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit();
    }
}

// --- Function: Safely get session message and clear it ---
// Used to display one-time messages after redirect
function getAndClearMessage() {
    $message = $_SESSION['message'] ?? '';
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    return $message;
}

// --- Function: Set session message ---
// Used to pass message through redirect
function setMessage($message) {
    $_SESSION['message'] = $message;
}

?>
