<?php
$host = "localhost";  // Change if hosted remotely
$dbname = "venue_db";
$username = "root";  // Change if using another MySQL user
$password = "";  // Set your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
