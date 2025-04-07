<?php
session_start();


// Get user info from session
$firstname = $_SESSION['firstname'] ?? 'Guest';
$lastname = $_SESSION['lastname'] ?? '';
$client_address = $_SESSION['client_address'] ?? 'No Address Provided';

$venue = [
    "logo" => "/venue_locator/images/logo.png",
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 50;
        }

        .group:hover .dropdown-content {
            display: block;
        }
    </style>
</head>

<body class="bg-gray-100">
    <header class="bg-gray-800 p-4 flex justify-between items-center text-white">
        <div class="flex items-center">
            <img src="<?= $venue['logo'] ?>" alt="Ventech" class="h-10 w-10" width="40" height="40"/>
            <span class="ml-2 text-xl font-bold">Ventech Venues</span>
        </div>
        
        <nav class="flex space-x-4">
            <a class="hover:underline" href="#">Submit Venue</a>
            <a href="notifications.php" class="hover:underline flex items-center">
            <i class="fas fa-bell text-white text-lg"></i> <span class="ml-1">Notifications</span></a>

            
            <div class="relative group">
                <a class="hover:underline cursor-pointer flex items-center">
                    Explore <i class="fas fa-chevron-down ml-1"></i>
                </a>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-content absolute mt-2 py-2 w-48 bg-white text-black shadow-lg border border-gray-200 rounded">
                    <a href="/venue_locator/client/client_profile.php" class="block px-4 py-2 hover:bg-gray-200">My Account</a>
                    <a href="logout.php" class="block px-4 py-2 hover:bg-gray-200 text-red-600">Message</a>
                    <a href="/venue_locator/client/client_signout.php" class="block px-4 py-2 hover:bg-gray-200 text-red-600">Logout</a>
                </div>
            </div>

            <a href="client_dashboard.php" class="hover:underline">Home</a>
            <a href="#" class="hover:underline">Help</a>
        </nav>
    </header>

    <div class="text-center mt-10">
        <h1 class="text-2xl md:text-4xl font-bold text-black">
            Welcome, <?= htmlspecialchars($firstname . " " . $lastname); ?>!
        </h1>
        
        <p class="mt-2 text-sm md:text-base text-gray-600">
            Address: <?= htmlspecialchars($client_address); ?>
        </p>

        <p class="mt-4 text-sm md:text-base text-black max-w-lg mx-auto">
            You have successfully logged in.
        </p>
    </div>
</body>
</html>
