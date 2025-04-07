<?php 
session_start();
include 'config.php';

if (!isset($_SESSION['client_id'])) {
    echo "<script>alert('You must be logged in to edit your profile.'); window.location.href='client_signin.php';</script>";
    exit();
}

$client_id = $_SESSION['client_id'];

// Fetch client data
$stmt = $conn->prepare("SELECT firstname, lastname, username, email, client_address, password FROM client WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($firstname, $lastname, $username, $email, $client_address, $hashed_password);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_firstname = trim($_POST['first-name']);
    $new_lastname = trim($_POST['last-name']);
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_address = trim($_POST['client-address']);
    $old_password = $_POST['old-password'];
    $new_password = $_POST['new-password'];

    if (!empty($new_password)) {
        if (password_verify($old_password, $hashed_password)) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE client SET firstname=?, lastname=?, username=?, email=?, client_address=?, password=? WHERE id=?");
            $stmt->bind_param("ssssssi", $new_firstname, $new_lastname, $new_username, $new_email, $new_address, $hashed_new_password, $client_id);
        } else {
            echo "<script>alert('Old password is incorrect.');</script>";
            $stmt = null;
        }
    } else {
        $stmt = $conn->prepare("UPDATE client SET firstname=?, lastname=?, username=?, email=?, client_address=? WHERE id=?");
        $stmt->bind_param("sssssi", $new_firstname, $new_lastname, $new_username, $new_email, $new_address, $client_id);
    }

    if ($stmt && $stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='client_profile.php';</script>";
        exit();
    } elseif ($stmt) {
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
    <title>Edit Client Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="max-w-2xl mx-auto bg-white p-8 mt-10 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-center mb-6">Edit Client Profile</h1>
    <form method="POST" action="edit_profile_client.php">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="first-name" class="block text-sm font-medium">First Name</label>
                <input type="text" name="first-name" value="<?= htmlspecialchars($firstname); ?>" class="mt-1 w-full p-2 rounded border">
            </div>
            <div>
                <label for="last-name" class="block text-sm font-medium">Last Name</label>
                <input type="text" name="last-name" value="<?= htmlspecialchars($lastname); ?>" class="mt-1 w-full p-2 rounded border">
            </div>
            <div>
                <label for="username" class="block text-sm font-medium">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($username); ?>" class="mt-1 w-full p-2 rounded border">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email); ?>" class="mt-1 w-full p-2 rounded border">
            </div>
            <div class="md:col-span-2">
                <label for="client-address" class="block text-sm font-medium">Client Address</label>
                <input type="text" name="client-address" value="<?= htmlspecialchars($client_address); ?>" class="mt-1 w-full p-2 rounded border">
            </div>
            <div>
                <label for="old-password" class="block text-sm font-medium">Old Password</label>
                <input type="password" name="old-password" class="mt-1 w-full p-2 rounded border">
            </div>
            <div>
                <label for="new-password" class="block text-sm font-medium">New Password</label>
                <input type="password" name="new-password" class="mt-1 w-full p-2 rounded border">
            </div>
        </div>
        <div class="text-center mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded shadow">
                Update Profile
            </button>
        </div>
    </form>
</div>
</body>
</html>

