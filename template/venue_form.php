<?php
require 'config.php'; // Database connection file

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the next available venue ID
$nextVenueId = 1;
$result = $conn->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'venue_db' AND TABLE_NAME = 'venues'");
if ($result && $row = $result->fetch_assoc()) {
    $nextVenueId = $row['AUTO_INCREMENT'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $price = floatval($_POST['price']);
    $lat = floatval($_POST['lat']);
    $lng = floatval($_POST['lng']);
    $capacity = intval($_POST['capacity']);
    $category = htmlspecialchars(trim($_POST['category']));
    $category2 = htmlspecialchars(trim($_POST['category2']));
    $category3 = htmlspecialchars(trim($_POST['category3']));
    $created_at = $_POST['created_at'] ?? date('Y-m-d H:i:s');
    $status = $_POST['status'] ?? 'open'; // Default status to 'open'

    // **Location Fix: Validate Latitude & Longitude**
    if (!is_numeric($lat) || !is_numeric($lng) || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
        die("<script>alert('Invalid location! Please provide a valid latitude (-90 to 90) and longitude (-180 to 180).'); window.history.back();</script>");
    }

    // Validate and encode JSON tags
    $tagsInput = trim($_POST['tags']);
    $tagsArray = json_decode($tagsInput, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($tagsArray)) {
        die("<script>alert('Error: Tags must be in valid JSON format (e.g., [\"event\", \"wedding\", \"party\"]).'); window.history.back();</script>");
    }

    $tags = json_encode($tagsArray);

    // Ensure uploads directory exists
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle image upload with timestamp to prevent name conflicts
    $image = 'uploads/default_court.jpg';
    if (!empty($_FILES['image']['name'])) {
        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = time() . '_' . uniqid() . '.' . $fileExt;
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $targetFile;
        } else {
            die("<script>alert('Error: Failed to upload image.'); window.history.back();</script>");
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO venues (name, description, price, lat, lng, capacity, tags, category, category2, category3, image, created_at, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddiisisssss", $name, $description, $price, $lat, $lng, $capacity, $tags, $category, $category2, $category3, $image, $created_at, $status);

    if ($stmt->execute()) {
        echo "
        <div id='toast' class='fixed bottom-5 right-5 bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center space-x-4 z-50'>
            <div>
                <p class='font-semibold'>Venue added successfully!</p>
                <div class='mt-2 flex space-x-2'>
                    <button onclick=\"window.location.href='venue_info.php?id=$inserted_id'\" class='bg-white text-green-600 px-3 py-1 rounded hover:bg-gray-100 transition'>View Details</button>
                    <button onclick=\"window.location.href='venue_list.php'\" class='bg-white text-green-600 px-3 py-1 rounded hover:bg-gray-100 transition'>Go to List</button>
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('toast')?.remove();
            }, 10000); // Hide after 10 seconds
        </script>
        ";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { background-color: #1e293b; color: #cbd5e1; font-family: 'Courier New', Courier, monospace; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-4xl">
        <h2 class="text-2xl mb-4">Create Venue</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="venue_id" class="block text-sm mb-2">Venue ID</label>
                <input type="text" id="venue_id" name="venue_id" value="<?php echo $nextVenueId; ?>" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" readonly>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="block text-sm mb-2">Name</label>
                    <input type="text" id="name" name="name" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm mb-2">Description</label>
                    <textarea id="description" name="description" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-sm mb-2">Price</label>
                    <input type="number" step="0.01" id="price" name="price" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>
                <div class="mb-4">
                    <label for="lat" class="block text-sm mb-2">Latitude</label>
                    <input type="number" step="0.000001" id="lat" name="lat" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>
                <div class="mb-4">
                    <label for="lng" class="block text-sm mb-2">Longitude</label>
                    <input type="number" step="0.000001" id="lng" name="lng" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>

                <div class="mb-4 flex items-center">
                <button type="button" onclick="getLocationFromCoords()" class="p-2 bg-green-600 rounded text-white">Detect Location</button>
                </div>

                <div class="mb-4">
                <label for="location" class="block text-sm mb-2">Detected Location</label>
                <input type="text" id="location" name="location" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>
                    <label for="capacity" class="block text-sm mb-2">Capacity</label>
                    <input type="number" id="capacity" name="capacity" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>
                <div class="mb-4">
                    <label for="tags" class="block text-sm mb-2">Tags (JSON format)</label>
                    <input type="text" id="tags" name="tags" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>
                <div class="mb-4">
                    <label for="category" class="block text-sm mb-2">Category</label>
                    <input type="text" id="category" name="category" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                </div>
                <div class="mb-4">
                    <label for="category2" class="block text-sm mb-2">Category 2</label>
                    <select id="category2" name="category2" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                        <option value="low price">Low Price</option>
                        <option value="mid price">Mid Price</option>
                        <option value="high price">High Price</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="category3" class="block text-sm mb-2">Category 3</label>
                    <select id="category3" name="category3" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="10">10</option>
                        <option value="12">12</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                    </select>
                </div>
                <div class="mb-4">
                <label for="status" class="block text-sm mb-2">Status</label>
                <select id="status" name="status" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300" required>
                 <option value="open">Open</option>
                  <option value="closed">Closed</option>
                 </select>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-sm mb-2">Image Upload</label>
                    <input type="file" id="image" name="image" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300">
                </div>
                <div class="mb-4">
                    <label for="created_at" class="block text-sm mb-2">Created At</label>
                    <input type="datetime-local" id="created_at" name="created_at" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-300">
                </div>
            </div>
            <button type="submit" class="w-full p-2 bg-blue-600 rounded text-white">Submit</button>
        </form>
    </div>
    <script>
function getLocationFromCoords() {
    var lat = document.getElementById("lat").value;
    var lng = document.getElementById("lng").value;

    if (lat && lng) {
        var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;

        fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById("location").value = data.display_name;
            } else {
                alert("Location not found!");
            }
        })
        .catch(error => {
            console.error("Error fetching location:", error);
        });
    } else {
        alert("Please enter valid latitude and longitude.");
    }
}
</script>


</body>
</html>
