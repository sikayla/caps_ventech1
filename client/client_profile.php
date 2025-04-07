<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT firstname, lastname, username, email, client_address, created_at FROM client WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$joined_date = isset($user['created_at']) ? date("F Y", strtotime($user['created_at'])) : 'Unknown';
$profile_pic = "/venue_locator/images/logo.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Client Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "/venue_locator/template/index.php";
            }
        }
    </script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
<header class="bg-gray-800 p-4 flex justify-between items-center">
    <div class="flex items-center">
        <img alt="Ventech" class="h-10 w-10" src="<?= $venue_logo ?>" width="40" height="40"/>
        <span class="ml-2 text-xl font-bold text-white">Ventech Venues</span>
    </div>
    <nav class="flex space-x-4 relative z-50 text-white">
        <a class="hover:underline" href="/venue_locator/template/client/venue_form.php">Submit Venue</a>

        <div class="relative group">
            <a class="hover:underline cursor-pointer flex items-center" href="#">
                Explore <i class="fas fa-chevron-down ml-1"></i>
            </a>
            <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
                <a href="/venue_locator/template/client/client_profile.php" class="block px-4 py-2 hover:bg-gray-200">My Account</a>
                <a href="messages.php" class="block px-4 py-2 hover:bg-gray-200">Messages</a>
                <a href="logout.php" class="block px-4 py-2 hover:bg-gray-200 text-red-600">Logout</a>
            </div>
        </div>

        <a href="notifications.php" class="hover:underline flex items-center">
            <i class="fas fa-bell text-white text-lg"></i> <span class="ml-1">Notifications</span>
        </a>

        <a href="/venue_locator/template/client/client_dashboard.php" class="hover:underline">Home</a>
        <a href="#" class="hover:underline">Help</a>
    </nav>
</header>

<main class="max-w-2xl mx-auto mt-10 bg-white rounded-lg shadow-lg p-6">
    <div class="text-center">
        <img src="<?= $profile_pic ?>" alt="Profile Picture" class="w-32 h-32 rounded-full mx-auto mb-4">
        <h2 class="text-2xl font-bold text-gray-800">
            <?= htmlspecialchars($user['firstname']) . " " . htmlspecialchars($user['lastname']); ?>
        </h2>
        <p class="text-gray-600">@<?= htmlspecialchars($user['username']); ?></p>
        <p class="text-gray-500 text-sm mb-2">Joined <?= $joined_date; ?></p>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Contact Info</h3>
        <ul class="text-gray-600">
            <li><i class="fas fa-envelope mr-2 text-orange-500"></i><?= htmlspecialchars($user['email']); ?></li>
            <li><i class="fas fa-map-marker-alt mr-2 text-orange-500"></i><?= htmlspecialchars($user['client_address'] ?? 'No address provided'); ?></li>
        </ul>
    </div>

    <div class="mt-6 flex justify-between">
        <a href="edit_profile_client.php" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded">
            Edit Profile
        </a>
        <a href="/venue_locator/template/client/client_dashboard.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
</main>
</body>
</html>
