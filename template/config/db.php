<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Change if using a different user
$password = ""; // Change if there's a database password
$dbname = "venue_db"; // Ensure this matches your actual DB name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
