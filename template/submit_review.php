<?php
session_start();
include 'config.php'; // Database connection

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venue_id = $_POST['venue_id'] ?? null;
    $reviewer_name = trim($_POST['reviewer_name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // Validate data
    if (!$venue_id || empty($reviewer_name) || empty($comment) || $rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO venue_reviews (venue_id, reviewer_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $venue_id, $reviewer_name, $rating, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Review submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
}
?>
