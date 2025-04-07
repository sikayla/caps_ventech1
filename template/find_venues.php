<?php
require '../config.php';

header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get filter parameters from URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 1000000;
$min_capacity = isset($_GET['min_capacity']) ? (int)$_GET['min_capacity'] : 0;

// Build query with filters
$query = "SELECT id, name, lat, lng, price, capacity, image FROM venues WHERE 1";

// Search filter
if (!empty($search)) {
    $query .= " AND name LIKE '%$search%'";
}

// Price range filter
$query .= " AND price BETWEEN $min_price AND $max_price";

// Capacity filter
$query .= " AND capacity >= $min_capacity";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "Failed to fetch data"]);
    exit;
}

$venues = [];

while ($row = mysqli_fetch_assoc($result)) {
    $venues[] = $row;
}

echo json_encode($venues);
?>
