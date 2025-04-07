<?php 
// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Handle DELETE venue request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM venues WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('✅ Venue deleted successfully!'); window.location.href='manage_venues.php';</script>";
    }
}

// Handle CONFIRM venue request
if (isset($_GET['confirm'])) {
    $id = intval($_GET['confirm']);
    $stmt = $conn->prepare("UPDATE venues SET admin_status = 'confirmed' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('✅ Venue confirmed successfully!'); window.location.href='manage_venues.php';</script>";
    }
}

// Handle REJECT venue request
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $stmt = $conn->prepare("UPDATE venues SET admin_status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('❌ Venue rejected!'); window.location.href='manage_venues.php';</script>";
    }
}

// Fetch all venues from the database
$sql = "SELECT * FROM venues ORDER BY created_at DESC";
$result = $conn->query($sql);
if (!$result) {
    die("❌ Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Venues</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 p-4">
    <div class="container mx-auto">
        <h2 class="text-center text-2xl font-bold mb-6">Manage Venues</h2>

        <!-- Search & Filter Section -->
        <div class="mb-4">
            <form method="GET" class="flex items-center justify-between bg-white p-4 shadow rounded">
                <input type="text" name="search" placeholder="Search by Name or Location" 
                       class="border p-2 w-2/3" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <select name="status" class="border p-2">
                    <option value="">All Status</option>
                    <option value="pending" <?= isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?= isset($_GET['status']) && $_GET['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="rejected" <?= isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Image</th>
                        <th class="py-2 px-4 border-b">Name</th>
                        <th class="py-2 px-4 border-b">Price</th>
                        <th class="py-2 px-4 border-b">Capacity</th>
                        <th class="py-2 px-4 border-b">Location</th>
                        <th class="py-2 px-4 border-b">Latitude</th>
                        <th class="py-2 px-4 border-b">Longitude</th>
                        <th class="py-2 px-4 border-b">Description</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 border-b text-center"><?= htmlspecialchars($row['id']); ?></td>
                            <td class="py-2 px-4 border-b text-center">
                                <img src="<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'uploads/default_court.jpg'; ?>" 
                                     alt="<?= htmlspecialchars($row['name']); ?>" class="w-16 h-16 rounded">
                            </td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['name']); ?></td>
                            <td class="py-2 px-4 border-b">₱<?= number_format($row['price'], 2); ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['capacity']); ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['location'] ?? 'Unknown'); ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['lat'] ?? 'N/A'); ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['lng'] ?? 'N/A'); ?></td>
                            <td class="py-2 px-4 border-b"><?= substr(htmlspecialchars($row['description']), 0, 50); ?>...</td>
                            <td class="py-2 px-4 border-b text-center">
                                <span class="px-2 py-1 rounded text-white <?= $row['admin_status'] === 'confirmed' ? 'bg-green-500' : ($row['admin_status'] === 'pending' ? 'bg-yellow-500' : 'bg-red-500'); ?>">
                                    <?= ucfirst($row['admin_status']); ?>
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b text-center">
                                <?php if ($row['admin_status'] === 'pending'): ?>
                                    <a href="manage_venues.php?confirm=<?= $row['id']; ?>" class="text-green-500 mr-2">
                                        <i class="fas fa-check-circle"></i> Confirm
                                    </a>
                                    <a href="manage_venues.php?reject=<?= $row['id']; ?>" class="text-red-500 mr-2">
                                        <i class="fas fa-times-circle"></i> Reject
                                    </a>
                                <?php endif; ?>
                                <a href="edit_venue.php?id=<?= $row['id']; ?>" class="text-blue-500 mr-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="manage_venues.php?delete=<?= $row['id']; ?>" class="text-red-500" 
                                   onclick="return confirm('Are you sure you want to delete this venue?');">
                                    <i class="fas fa-trash"></i> Delete
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

