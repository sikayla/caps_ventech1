<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if venue ID is provided
if (!isset($_GET['venue_id'])) {
    die("Venue ID not provided.");
}

$venue_id = $_GET['venue_id'];

// Fetch venue details
$sql = "SELECT * FROM venue_details WHERE venue_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();
$venue = $result->fetch_assoc();

if (!$venue) {
    die("Venue not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Header Section -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <img src="<?php echo $venue['header_image']; ?>" class="w-full h-60 object-cover rounded mb-4">
            <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($venue['venue_name']); ?></h1>
            <p class="text-gray-600">Venue ID: <?php echo htmlspecialchars($venue['venue_id']); ?></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4">
                        <i class="fas fa-camera"></i> Photo Gallery
                    </h2>
                    <div class="grid grid-cols-3 gap-2">
                        <?php
                        $gallery_images = explode(",", $venue['gallery_images']);
                        foreach ($gallery_images as $image) {
                            echo '<img src="' . $image . '" class="w-full h-32 object-cover rounded">';
                        }
                        ?>
                    </div>
                </div>

                <div class="bg-white p-4 rounded shadow mt-4">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4">Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($venue['description'])); ?></p>
                </div>

                <div class="bg-white p-4 rounded shadow mt-4">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4">
                        <i class="fas fa-video"></i> Video Tour
                    </h2>
                    <video class="w-full rounded" controls>
                        <source src="<?php echo $venue['video_tour']; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-lg font-bold mb-2">Contact Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($venue['owner_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($venue['phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($venue['email']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($venue['address']); ?></p>
                    <a href="<?php echo htmlspecialchars($venue['map_url']); ?>" class="text-blue-600">
                        <i class="fas fa-map-marker-alt"></i> Get Directions
                    </a>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-lg font-bold mb-2">
                        <i class="fas fa-share-alt"></i> Social Profiles
                    </h3>
                    <p><a href="<?php echo htmlspecialchars($venue['facebook']); ?>" class="text-blue-600"><i class="fab fa-facebook"></i> Facebook</a></p>
                    <p><a href="<?php echo htmlspecialchars($venue['twitter']); ?>" class="text-blue-600"><i class="fab fa-twitter"></i> Twitter</a></p>
                    <p><a href="<?php echo htmlspecialchars($venue['instagram']); ?>" class="text-blue-600"><i class="fab fa-instagram"></i> Instagram</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
