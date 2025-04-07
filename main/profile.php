<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT firstname, lastname, username, email, school_name, created_at FROM user_admin WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Database error: " . $conn->error);
}

// Format registration date
$joined_date = isset($user['created_at']) ? date("F Y", strtotime($user['created_at'])) : 'Unknown';

// Default profile picture
$profile_pic = "/venue_locator/images/logo.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Player Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .flex{
          color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
<header class="bg-gray-800 p-4 flex justify-between items-center">
        <div class="flex items-center">
            <img alt="Ventech" class="h-10 w-10" src="<?= $venue['logo'] ?>" width="40" height="40"/>
            <span class="ml-2 text-xl font-bold">ventech venues</span>
        </div>
        <nav class="flex space-x-4 relative z-50">
    <a class="hover:underline" href="#">Submit Venue</a>
    <a href="dashboard.php" class="hover:underline">Home</a>

    <div class="relative group">
        <a class="hover:underline cursor-pointer flex items-center" href="#">
            Profile <i class="fas fa-chevron-down ml-1"></i>
        </a>

        <!-- Dropdown Menu -->
        <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
            <a href="profile.php" class="block px-4 py-2 hover:bg-gray-200">Account</a>
            
        </div>
    </div>

    <a href="#" class="hover:underline">Help</a>
    <a href="signin.php" class="hover:underline">Sign In</a>
</nav>

    </header>
    <div class="text-center">
        <h1 class="text-3xl font-bold mb-6">
            Player Profile of <span class="text-orange-500"><?= htmlspecialchars($user['username']); ?></span>
        </h1>
        <div class="bg-white shadow-md rounded-lg p-6 max-w-xs mx-auto">
            <img src="<?= $profile_pic; ?>" alt="Default Profile Picture" class="rounded-full mx-auto mb-4" height="200" width="200"/>
            <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($user['firstname']) . " " . htmlspecialchars($user['lastname']); ?></h2>
            <div class="flex justify-center space-x-2 mb-4">
                <span class="bg-orange-200 text-orange-600 px-2 py-1 rounded-full text-sm">
                    <?= htmlspecialchars($user['school_name']); ?>
                </span>
            </div>
            <p class="text-gray-600 mb-4">
                Joined <?= $joined_date; ?>.
                <br/>
            </p>
            <div class="flex justify-between">
                <a href="edit_profile.php" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                    Update Profile
                </a>
                <a href="/venue_locator/template/index.php" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                    Logout
                </a>
            </div>
        </div>
    </div>
</body>
</html>


