<?php
session_start();
include 'config.php';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Sanitize inputs
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $client_address = trim($_POST['client_address']);

    // Basic Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format. Please try again.'); window.history.back();</script>";
        exit();
    }

    if (strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.'); window.history.back();</script>";
        exit();
    }

    // Ensure database connection is established
    if (!isset($conn) || $conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM client WHERE username = ? OR email = ?");
    if (!$stmt) {
        die("SQL Error (SELECT): " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Error: Username or Email already exists. Please try another.'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // Password hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO client (firstname, lastname, username, email, password, client_address) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL Error (INSERT): " . $conn->error);
    }

    $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $hashed_password, $client_address);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please log in.'); window.location.href='client_signin.php';</script>";
    } else {
        die("Insert Error: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-2xl bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center mb-2">Create Your Account</h1>
        <p class="text-center mb-6">Become a registered user and enjoy exclusive benefits!</p>
        <form method="POST" action="client_signup.php">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">First Name *</label>
                    <input type="text" name="firstname" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Name *</label>
                    <input type="text" name="lastname" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username *</label>
                    <input type="text" name="username" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Client Address *</label>
                    <input type="text" name="client_address" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>
            </div>
            <div class="mb-4">
                <input type="checkbox" name="terms" class="mr-2" required>
                <label class="text-sm text-gray-700">You accept our <a href="#" class="text-orange-500">Terms of Use</a>, <a href="#" class="text-orange-500">Privacy Policy</a>, and <a href="#" class="text-orange-500">Cookie Policy</a>.</label>
            </div>
            <div class="text-center">
                <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-md">Create Your Account</button>
            </div>
        </form>
    </div>
</body>
</html>
