<?php
session_start();
$configPath = realpath(__DIR__ . "/../config.php");
if ($configPath && file_exists($configPath)) {
    include $configPath;
} else {
    die("Error: Configuration file not found.");
}

if (!isset($conn)) {
    die("Error: Database connection failed.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch clients from the database
$clientsQuery = $conn->query("SELECT * FROM client");

// Check if query was successful
if (!$clientsQuery) {
    die("SQL Error: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Manage Clients</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($client = $clientsQuery->fetch_assoc()): ?>
                <tr>
                    <td><?= $client['id']; ?></td>
                    <td><?= htmlspecialchars($client['firstname']); ?></td>
                    <td><?= htmlspecialchars($client['lastname']); ?></td>
                    <td><?= htmlspecialchars($client['email']); ?></td>
                    <td><?= htmlspecialchars($client['username']); ?></td>
                    <td><?= htmlspecialchars($client['client_address']); ?></td>
                    <td>
                        <a href="edit_client.php?id=<?= $client['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

