<?php
$servername = "localhost";  // Change if using a remote server
$username = "root";         // Change if using a different database user
$password = "";             // Set the database password
$dbname = "venue_db";       // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
