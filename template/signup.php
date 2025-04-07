<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $firstname = trim($_POST['first-name']);
    $lastname = trim($_POST['last-name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $school_name = trim($_POST['school-name']); // New school name field

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM user_admin WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Error: Username or Email already exists. Please try another.'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // Password hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO user_admin (firstname, lastname, username, email, password, school_name) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $hashed_password, $school_name);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please log in.'); window.location.href='signin.php';</script>";
    } else {
        echo "<script>alert('Error: Something went wrong. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-2xl bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center mb-2">Create Your Account on</h1>
        <h2 class="text-2xl font-bold text-center text-orange-500 mb-4">Courts of the World</h2>
        <p class="text-center mb-6">Enjoy the benefits of becoming a registered user.<br>Create your profile, add your homecourt, comment on courts, and post your photos and videos!</p>
        <form method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Name *</label>
                    <input type="text" name="first-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Name *</label>
                    <input type="text" name="last-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username *</label>
                    <input type="text" name="username" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">School Name *</label>
                    <input type="text" name="school-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
            </div>
            <div class="mb-4">
                <input type="checkbox" name="terms" class="mr-2" required>
                <label class="text-sm text-gray-700">You accept our <a href="#" class="text-orange-500">Terms of Use</a>, <a href="#" class="text-orange-500">Privacy Policy</a>, and <a href="#" class="text-orange-500">Cookie Policy</a>.</label>
            </div>
            <div class="mb-6">
                <input type="checkbox" name="updates" class="mr-2">
                <label class="text-sm text-gray-700">You agree to receive updates via newsletter.</label>
            </div>
            <p class="text-sm text-gray-700 mb-4">* Mandatory fields.</p>
            <div class="text-center">
                <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-md">Create Your Account</button>
            </div>
        </form>
    </div>
</body>
</html>
