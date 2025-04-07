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

if (!isset($_GET['id'])) {
    die("Error: Client ID not provided.");
}

$clientId = intval($_GET['id']);
$clientQuery = $conn->query("SELECT * FROM clients WHERE id = $clientId");
$client = $clientQuery->fetch_assoc();

if (!$client) {
    die("Error: Client not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $client_address = $conn->real_escape_string($_POST['client_address']);

    $updateQuery = "UPDATE clients SET firstname='$firstname', lastname='$lastname', email='$email', 
                    username='$username', client_address='$client_address' WHERE id = $clientId";

    if ($conn->query($updateQuery)) {
        header("Location: manage_clients.php?success=Client updated successfully");
        exit();
    } else {
        $error = "Failed to update client details.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Client</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($client['firstname']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($client['lastname']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($client['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="client_address" class="form-control" value="<?= htmlspecialchars($client['client_address']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Client</button>
        <a href="manage_clients.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
