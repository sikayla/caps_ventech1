<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Get user info from session
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];

$venue = [
    
    "logo" => "/venue_locator/images/logo.png",
    
  ];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Welcome</title>
    <style>
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
    

    <div class="relative group">
    <a class="hover:underline cursor-pointer flex items-center" href="#">
            Explore <i class="fas fa-chevron-down ml-1"></i>
        </a>

        <!-- Dropdown Menu -->
        <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
            <a href="/venue_locator/main/profile.php" class="block px-4 py-2 hover:bg-gray-200">My Account</a>
            
        </div>
    </div>
    <a href="/venue_locator/main/dashboard.php" class="hover:underline">Home</a>
    <a href="#" class="hover:underline">Help</a>
    <a href="signin.php" class="hover:underline">Sign In</a>
</nav>

    </header>
    <div class="text-center">
        <h1 class="text-2xl md:text-4xl font-bold text-black">
            Welcome, <?php echo htmlspecialchars($firstname . " " . $lastname); ?>!
        </h1>
        <p class="mt-4 text-sm md:text-base text-black max-w-lg mx-auto">
            You have successfully logged in.
        </p>
        
    </div>
</body>
</html>