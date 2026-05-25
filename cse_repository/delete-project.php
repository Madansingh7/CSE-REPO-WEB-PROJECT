<?php
// ============================================================
//  delete-project.php - Delete a Project
//  This file does NOT have an HTML page.
//  It just runs the DELETE query and then redirects.
//
//  It is triggered by a form POST from any page:
//    <form method="POST" action="delete-project.php">
//        <input type="hidden" name="id" value="5">
//        <input type="hidden" name="redirect" value="projects.php">
//        <button type="submit">Delete</button>
//    </form>
// ============================================================

include 'db.php';

// --- Step 1: Only allow POST requests (not direct URL access) ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If someone tries to visit delete-project.php directly, redirect
    header("Location: projects.php");
    exit();
}

// --- Step 2: Get the project ID from the form ---
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header("Location: projects.php");
    exit();
}

$id = (int)$_POST['id']; // Convert to integer for safety

// --- Step 3: Get the redirect target ---
// redirect field tells us where to go after deleting
// Default is projects.php if not specified
$redirect = $_POST['redirect'] ?? 'projects.php';

// Safety: only allow redirect to our own pages
$allowedRedirects = ['projects.php', 'index.php'];
if (!in_array($redirect, $allowedRedirects)) {
    $redirect = 'projects.php';
}

// --- Step 4: Run the DELETE query ---
// DELETE FROM table WHERE condition
// The WHERE id = $id is VERY IMPORTANT - without it, ALL rows get deleted!
$deleteQuery = "DELETE FROM projects WHERE id = $id";
$deleteResult = mysqli_query($conn, $deleteQuery);

// --- Step 5: Redirect with a status message ---
if ($deleteResult && mysqli_affected_rows($conn) > 0) {
    // mysqli_affected_rows() returns how many rows were deleted
    // Redirect with success message in URL
    header("Location: $redirect?deleted=1");
} else {
    // Something went wrong
    header("Location: $redirect?deleted=0");
}

// Close connection
mysqli_close($conn);

// exit() ensures no more code runs after redirect
exit();
?>
