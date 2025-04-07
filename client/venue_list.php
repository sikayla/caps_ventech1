<?php  
// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Base URL for images
$base_url = "uploads/";

// Fetch **only the latest venue**
$sql = "SELECT v.*, vd.venue_id AS details_exist 
        FROM venues v 
        LEFT JOIN venue_details vd ON v.id = vd.venue_id 
        ORDER BY v.id DESC 
        LIMIT 1";  // ✅ This ensures only **1 venue** is fetched

$result = $conn->query($sql);
$venue = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Featured Venue</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">

  <div class="container mx-auto py-8 flex justify-center">
    <?php if ($venue): ?>
      <?php 
        // Decode JSON tags safely
        $tags = json_decode($venue["tags"], true);
        $tags_display = is_array($tags) ? implode(", ", $tags) : "No Tags";

        // Fix Image Path Handling
        $imagePath = $venue["image"];
        $image = (!empty($imagePath) && file_exists(__DIR__ . "/" . $imagePath)) 
            ? $imagePath 
            : "uploads/default_court.jpg";

        // Determine the correct link for viewing venue details
        $view_link = $venue['details_exist'] 
            ? "venue_display.php?venue_id={$venue["id"]}" 
            : "venue_info.php?venue_id={$venue["id"]}";

        // Format the price
        $price = ($venue["price"] > 0) ? "₱" . number_format($venue["price"], 2) : "Price not available";
        $capacity = $venue["capacity"];
        $location = $venue["location"] ?? "Location not available"; 
        $description = $venue["description"] ?? "No description available";
        $lat = $venue["lat"] ?? "Not available";
        $lng = $venue["lng"] ?? "Not available";
        $rating = $venue["rating"] ?? "No rating";
        $ratings_count = $venue["ratings_count"] ?? 0;

        // Highlight status
        $status = strtolower($venue["status"] ?? "open"); 
        $status_color = ($status === "open") ? "bg-green-500" : "bg-red-500";
      ?>

      <div class="bg-white rounded-lg shadow-md overflow-hidden w-96">
        <div class="relative">
          <img src="<?= $image; ?>" alt="<?= htmlspecialchars($venue["name"]); ?>" class="w-full h-64 object-cover"/>
          <div class="absolute top-2 left-2 px-2 py-1 text-white text-xs font-semibold rounded <?= $status_color; ?>">
            <?= ucfirst($status); ?>
          </div>
          <div class="absolute top-2 right-2 bg-white p-1 rounded-full text-xs font-semibold">
            <i class="fas fa-heart"></i>
          </div>
        </div>
        <div class="p-4">
          <h2 class="text-lg font-bold mb-1"><?= htmlspecialchars($venue["name"]); ?></h2>
          <p class="text-gray-600 text-sm mb-2">Outdoor / Public</p>
          <div class="flex items-center mb-2">
            <span class="bg-red-100 text-red-500 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded"><?= htmlspecialchars($location); ?></span>
          </div>
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">Latitude: <?= $lat; ?></span>
            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">Longitude: <?= $lng; ?></span>
            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">Price: <?= $price; ?></span>
            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">Capacity: <?= $capacity; ?></span>
          </div>
          <p class="text-gray-700 text-sm"><?= substr(htmlspecialchars($description), 0, 100); ?>...</p>
          <div class="flex items-center justify-between mt-4">
            <a href="<?= $view_link; ?>" class="bg-orange-500 text-white text-sm font-semibold px-4 py-2 rounded">
              View Court <i class="fas fa-arrow-right ml-1"></i>
            </a>
            <a href="venue_details.php?id=<?= $venue["id"]; ?>" class="bg-blue-500 text-white text-sm font-semibold px-4 py-2 rounded">
              Book Now <i class="fas fa-calendar-check ml-1"></i>
            </a>
            <div class="flex items-center">
              <i class="fas fa-star text-yellow-500 mr-1"></i>
              <span class="text-gray-800 text-sm font-semibold"><?= $rating; ?></span>
              <span class="text-gray-600 text-sm ml-1">(<?= $ratings_count; ?> ratings)</span>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <p class="text-gray-600 text-center">No venues available.</p>
    <?php endif; ?>
  </div>
</body>
</html>
