<?php
// ============================================================
//  logout.php - Logout User
//  Destroys the session and redirects to home page
// ============================================================

include 'auth.php';

// Destroy all session data
session_destroy();

// Redirect to home page with logout message
header("Location: index.php?logged_out=1");
exit();
?>
