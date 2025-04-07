<?php
include 'config.php'; // Your database connection
session_start();

// Make sure the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Find the user in the database
    $query = "SELECT * FROM client WHERE username = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $client = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $client['password'])) {
            // Successful login
            $_SESSION['client_id'] = $client['client_id'];
            $_SESSION['username'] = $client['username'];

            // Redirect to dashboard
            header("Location: client_dashboard.php");
            exit();
        } else {
            // Incorrect password
            echo "Incorrect password.";
        }
    } else {
        // No user found
        echo "No such user.";
    }
} else {
    // If not POST, redirect back to sign in page
    header("Location: client_signin.php");
    exit();
}
?>
