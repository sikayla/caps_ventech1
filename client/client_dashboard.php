<?php 
include 'config.php';
session_start();


// Fetch venues owned by the logged-in client
$query = "SELECT * FROM client WHERE client_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL Error (prepare): " . $conn->error);
}

$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("SQL Error (execute): " . $stmt->error);
}

$venues = $result; // Store the result for use later in the template

// Set the venue logo (you wrote it incorrectly, fixed below)
$venue_logo = "/venue_locator/images/logo.png"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">

<header class="bg-gray-800 p-4 flex justify-between items-center">
    <div class="flex items-center">
        <img alt="Ventech" class="h-10 w-10" src="<?= $venue_logo ?>" width="40" height="40"/>
        <span class="ml-2 text-xl font-bold text-white">Ventech Venues</span>
    </div>
    <nav class="flex space-x-4 relative z-50 text-white">
        <a class="hover:underline" href="/venue_locator/template/venue_form.php">Submit Venue</a>

        <div class="relative group">
            <a class="hover:underline cursor-pointer flex items-center" href="#">
                Explore <i class="fas fa-chevron-down ml-1"></i>
            </a>
            <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
                <a href="/venue_locator/client/client_profile.php" class="block px-4 py-2 hover:bg-gray-200">My Account</a>
                <a href="messages.php" class="block px-4 py-2 hover:bg-gray-200">Messages</a>
                <a href="/venue_locator/client/client_signout.php" class="block px-4 py-2 hover:bg-gray-200 text-red-600">Logout</a>
            </div>
        </div>

        <a href="notifications.php" class="hover:underline flex items-center">
            <i class="fas fa-bell text-white text-lg"></i> <span class="ml-1">Notifications</span>
        </a>

        <a href="/venue_locator/client/client_dashboard.php" class="hover:underline">Home</a>
        <a href="#" class="hover:underline">Help</a>
        
    </nav>
</header>

<main class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-center mb-6"><?= htmlspecialchars($title ?? 'Client Dashboard'); ?></h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Your Venues</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($venue = $result->fetch_assoc()): ?>
                    <div class="bg-gray-100 rounded-lg shadow-md overflow-hidden">
                        <img src="<?= htmlspecialchars($venue['image'] ?? 'default-image.jpg') ?>" 
                             alt="<?= htmlspecialchars($venue['name'] ?? 'Venue') ?>" 
                             class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($venue['name'] ?? 'Untitled Venue') ?></h3>
                            <p class="text-sm text-gray-600">
                                ₱<?= number_format($venue['price'] ?? 0, 2) ?> • 
                                <?= htmlspecialchars($venue['category'] ?? 'Uncategorized') ?>
                            </p>
                            <div class="flex justify-between mt-4">
                                <a href="edit_venue.php?id=<?= $venue['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                                <a href="delete_venue.php?id=<?= $venue['id'] ?>" class="text-red-500 hover:underline"
                                   onclick="return confirm('Are you sure you want to delete this venue?');">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">You haven't submitted any venues yet.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>




