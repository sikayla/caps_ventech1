<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed!"]));
}

// Fetch venues from the database
$sql = "SELECT * FROM venues ORDER BY id DESC";
$result = $conn->query($sql);

$venues = [];

if ($result->num_rows > 0) {
    while ($venue = $result->fetch_assoc()) {
        $venue["tags"] = json_decode($venue["tags"]); // Decode JSON tags
        $venues[] = $venue;
    }
}

// Return venues as JSON
echo json_encode(["status" => "success", "venues" => $venues]);

$conn->close();
?>
