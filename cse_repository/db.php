<?php
// ============================================================
//  db.php - Database Connection File
//  This file connects PHP to the MySQL database.
//  Include this file in every PHP page that needs the database.
// ============================================================

// --- Database credentials ---
$host     = "localhost";   // WAMP runs MySQL on localhost
$user     = "root";        // Default WAMP username
$password = "";            // Default WAMP password is empty string
$database = "cse_repository"; // Our database name

// --- Create the connection ---
// mysqli_connect(host, username, password, database)
$conn = mysqli_connect($host, $user, $password, $database);

// --- Check if connection failed ---
if (!$conn) {
    // die() stops the script and shows an error message
    die("<h2 style='color:red; font-family:Arial; padding:20px;'>
        ❌ Database Connection Failed!<br><br>
        Error: " . mysqli_connect_error() . "<br><br>
        <strong>Fix:</strong> Make sure WAMP is running and the database 
        'cse_repository' exists in phpMyAdmin.
    </h2>");
}

// If we reach here, connection was successful!
// $conn is now available in any file that includes db.php
?>
