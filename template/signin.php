<?php
session_start();
include 'config.php'; // Ensure config.php correctly connects to your database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['email']); // Accepts email or username
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Query the database to check if the username or email exists
        $sql = "SELECT id, username, firstname, lastname, password FROM user_admin WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname']; // ✅ Fix: Store Firstname
            $_SESSION['lastname'] = $user['lastname'];   // ✅ Fix: Store Lastname

            header("Location: greet.php");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-gray-900 text-white p-4 flex justify-between items-center">
        <div class="flex items-center">
            <img src="/venue_locator/images/logo.png" alt="Primo Venues Logo" class="mr-2" width="40" height="40" />
            <span class="text-xl font-bold">primovenues</span>
        </div>
    </header>
    
    <div class="bg-white shadow-lg rounded-lg p-8 flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6 w-full max-w-4xl mx-auto mt-10">
        <div class="flex flex-col items-center w-full md:w-1/2">
            <h2 class="text-xl font-bold mb-4">Log in with</h2>
            <a href="facebook-login.php"> 
                <button class="flex items-center justify-center w-48 py-2 mb-4 border rounded-lg">
                    <i class="fab fa-facebook-f text-blue-600 mr-2"></i> Facebook
                </button>
            </a>
            <a href="google-login.php">
                <button class="flex items-center justify-center w-48 py-2 border rounded-lg">
                    <i class="fab fa-google text-red-600 mr-2"></i> Google
                </button>
            </a>
        </div>
        
        <div class="flex flex-col items-center w-full md:w-1/2">
            <h2 class="text-xl font-bold mb-4">Or, use your COTW account</h2>

            <?php if (isset($error)) : ?>
                <div class="bg-red-500 text-white p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form class="w-full max-w-sm" action="signin.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email or Username</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="email" type="text" required>
                </div>
                <div class="mb-6 relative">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password" id="password" type="password" required>
                    <i class="fas fa-eye absolute top-3 right-3 cursor-pointer text-gray-600" onclick="togglePassword()"></i>
                </div>

                <!-- ✅ Fix: Removed the incorrect <a> tag wrapping the button -->
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none w-full" type="submit">
                    Log in
                </button>
            </form>

            <div class="flex justify-between w-full mt-4">
                <a class="text-red-500 hover:text-red-800" href="signup.php">Register here</a>
                <a class="text-red-500 hover:text-red-800" href="forgot_password.php">Forgot password?</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var icon = event.target;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
