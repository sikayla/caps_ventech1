<?php
session_start();
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to edit your profile.'); window.location.href='signin.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT firstname, lastname, username, email, school_name, password FROM user_admin WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($firstname, $lastname, $username, $email, $school_name, $hashed_password);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_firstname = trim($_POST['first-name']);
    $new_lastname = trim($_POST['last-name']);
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_school_name = trim($_POST['school-name']);
    $old_password = $_POST['old-password'];
    $new_password = $_POST['new-password'];

    // Check if the user provided the old password before changing it
    if (!empty($new_password)) {
        if (password_verify($old_password, $hashed_password)) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE user_admin SET firstname=?, lastname=?, username=?, email=?, school_name=?, password=? WHERE id=?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssssi", $new_firstname, $new_lastname, $new_username, $new_email, $new_school_name, $hashed_new_password, $user_id);
        } else {
            echo "<script>alert('Old password is incorrect. Please try again.');</script>";
            $stmt = null;
        }
    } else {
        // If password is not being changed, update only other fields
        $update_query = "UPDATE user_admin SET firstname=?, lastname=?, username=?, email=?, school_name=? WHERE id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssi", $new_firstname, $new_lastname, $new_username, $new_email, $new_school_name, $user_id);
    }

    // Execute update query
    if ($stmt && $stmt->execute()) {
      echo "<script>alert('Profile updated successfully!'); window.location.href='back.php';</script>";
      exit();
  }
   elseif ($stmt) {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
    
    if ($stmt) $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
<header class="bg-gray-800 p-4 flex justify-between items-center">
    <div class="flex items-center">
        <img alt="Ventech" class="h-10 w-10" src="<?= $venue['logo'] ?>" width="40" height="40"/>
        <span class="ml-2 text-xl font-bold text-white">Ventech Venues</span>
    </div>
    <nav class="flex space-x-4 relative z-50">
        <a class="hover:underline text-white" href="#">Submit Venue</a>
        <div class="relative group">
            <a class="hover:underline cursor-pointer flex items-center text-white" href="#">
                Profile <i class="fas fa-chevron-down ml-1"></i>
            </a>
            <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
                <a href="edit_profile.php" class="block px-4 py-2 hover:bg-gray-200">Account</a>
            </div>
        </div>
        <a href="#" class="hover:underline text-white">Help</a>
        <a href="signin.php" class="hover:underline text-white">Sign In</a>
    </nav>
</header>

<div class="w-full max-w-2xl bg-white p-8 rounded-lg shadow-lg mx-auto mt-10">
    <h1 class="text-2xl font-bold text-center mb-6">Your Profile</h1>
    <form method="POST" action="edit_profile.php">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="first-name" class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" id="first-name" name="first-name" value="<?= htmlspecialchars($firstname); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
            </div>
            <div>
                <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" id="last-name" name="last-name" value="<?= htmlspecialchars($lastname); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
            </div>
            <div>
                <label for="school-name" class="block text-sm font-medium text-gray-700">School Name</label>
                <input type="text" id="school-name" name="school-name" value="<?= htmlspecialchars($school_name); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
            </div>
            <div>
                <label for="old-password" class="block text-sm font-medium text-gray-700">Old Password</label>
                <input type="password" id="old-password" name="old-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
            </div>
            <div>
                <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="new-password" name="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
            </div>
        </div>
        <div class="mt-6">
            <label class="inline-flex items-center">
                <input type="checkbox" class="form-checkbox rounded text-indigo-600">
                <span class="ml-2 text-sm text-gray-600">You agree to receive updates via the Courts of the World newsletter.</span>
            </label>
        </div>
        <div class="text-center mt-6">
            <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-md">Update Profile</button>
        </div>
    </form>
</div>
</body>
</html>


