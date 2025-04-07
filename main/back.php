<?php
// Start session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-2xl font-bold mb-2">Your Profile</h1>
        <p class="mb-4">Thank you for updating your settings.</p>
        <a href="profile.php">
            <button class="bg-orange-500 text-white py-2 px-4 rounded">Back to your profile</button>
        </a>
    </div>
</body>
</html>
