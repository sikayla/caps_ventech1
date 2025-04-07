<?php
// Dynamically find the correct path to config.php
$possible_paths = [
    __DIR__ . '/../../config.php',   // When inside /venue_locator/template/admin/
    __DIR__ . '/../config.php',      // When inside /venue_locator/template/
    __DIR__ . '/../../../config.php' // If the structure is different
];

$configPath = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $configPath = $path;
        break;
    }
}

if (!$configPath) {
    die("❌ Error: Configuration file not found. Check the path in admin_record.php.");
}

include $configPath;

// Ensure database connection is valid
if (!isset($conn) || $conn->connect_error) {
    die("❌ Database connection failed: " . ($conn->connect_error ?? "Connection object not set."));
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM user_admin WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='admin_record.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!');</script>";
    }
}

// Fetch user records
$result = $conn->query("SELECT * FROM user_admin");
if (!$result) {
    die("❌ Query failed: " . $conn->error);
}

// Venue logo
$venue = [
    "logo" => "/venue_locator/images/logo.png",
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 p-4">

<!-- Header -->
<header class="bg-gray-800 p-4 flex justify-between items-center">
    <div class="flex items-center">
        <img alt="Ventech" class="h-10 w-10" src="<?= $venue['logo'] ?>" width="40" height="40"/>
        <span class="ml-2 text-xl font-bold text-white">Ventech Venues</span>
    </div>
    <nav class="flex space-x-4 relative z-50 text-white">
        <a class="hover:underline" href="#">Submit Venue</a>
        <div class="relative group">
            <a class="hover:underline cursor-pointer flex items-center">
                Explore <i class="fas fa-chevron-down ml-1"></i>
            </a>
            <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
                <a href="profile.php" class="block px-4 py-2 hover:bg-gray-200">My Account</a>
            </div>
        </div>
        <a href="#" class="hover:underline">Help</a>
        <a href="signout.php" class="hover:underline">Sign-out</a>
    </nav>
</header>

<!-- Admin Records Table -->
<div class="container mx-auto mt-6">
    <h2 class="text-center text-2xl font-bold mb-4">Admin Records</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">First Name</th>
                    <th class="py-2 px-4 border-b">Last Name</th>
                    <th class="py-2 px-4 border-b">Username</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Profile Image</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4 border-b text-center"><?= htmlspecialchars($row['id']); ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['firstname']); ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['lastname']); ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['username']); ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['email']); ?></td>
                        <td class="py-2 px-4 border-b text-center">
                            <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'uploads/default.png'; ?>" 
                                 alt="Profile Image" class="h-10 w-10 rounded-full mx-auto">
                        </td>
                        <td class="py-2 px-4 border-b text-center">
                            <a href="edit_admin.php?id=<?= $row['id']; ?>" class="text-green-500 mx-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="admin_record.php?delete=<?= $row['id']; ?>" 
                               class="text-red-500 mx-2"
                               onclick="return confirm('Are you sure you want to delete this user?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

