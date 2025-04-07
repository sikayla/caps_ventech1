<?php 
// Database Configuration
$host = "localhost";
$user = "root"; // Change if needed
$pass = "";
$dbname = "venue_db"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Unique Categories, Prices, and Capacities in One Query (Optimized)
$query = "SELECT DISTINCT category, category2 AS price_range, category3 AS capacity FROM venues";
$filterResult = $conn->query($query);

// Fetch All Venues (Optimized)
$sql = "SELECT * FROM venues ORDER BY created_at DESC";
$venues = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$image_url = [ "logo" => "/venue_locator/images/logo.png" ]; // Fix for logo usage

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Venues</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #1a1a2e; }
        .dropdown-menu { max-height: 300px; overflow-y: auto; }
        .venue-card { display: block; } /* Default display */
        @media (min-width: 1024px) {
            .lg\:grid-cols-2 { grid-template-columns: repeat(3, minmax(0, 1fr)); width: 150%; margin-left: -25%; }
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="bg-gray-900 text-white">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="flex items-center">
                <img src="<?= $image_url['logo'] ?>" alt="Primo Venues Logo" class="h-10 w-10"/>
                <span class="ml-2 text-xl font-bold">Primo Venues</span>
            </div>
            <nav class="flex space-x-4">
            
                <a class="hover:underline" href="#">Submit Venue</a>
                <div class="relative group">
                    <a class="hover:underline" href="#">Explore <i class="fas fa-chevron-down"></i></a>
                    <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg">
                        <a href="index.php" class="block px-4 py-2 hover:bg-gray-200">Home</a>
                        <a href="list_venues.php" class="block px-4 py-2 hover:bg-gray-200">List Venues</a>
                        <a href="manage_bookings.php" class="block px-4 py-2 hover:bg-gray-200">Bookings</a>
                        <a href="find.php" class="block px-4 py-2 hover:bg-gray-200">Find Venues</a>
                    </div>
                </div>
                <a href="#" class="hover:underline">Help</a>
                <a href="signin.php" class="hover:underline">Sign In</a>
            </nav>
        </div>
    </header>

 

    <!-- Venue Cards -->
    <div class="container mx-auto py-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php
        // Fetch venues and check if details exist
        $sql = "SELECT v.*, vd.venue_id AS details_exist 
                FROM venues v 
                LEFT JOIN venue_details vd ON v.id = vd.venue_id 
                ORDER BY v.id DESC";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($venue = $result->fetch_assoc()) {
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
                    $location = $venue["location"] ?? "Location not available"; // Fetch the location from the database
                    $description = $venue["description"] ?? "No description available";
                    $lat = $venue["lat"] ?? "Not available";
                    $lng = $venue["lng"] ?? "Not available";
                    $rating = $venue["rating"] ?? "No rating";
                    $ratings_count = $venue["ratings_count"] ?? 0;
                    
                    // Highlight status
                    $status = strtolower($venue["status"] ?? "open"); // Default to "open"
                    $status_color = ($status === "open") ? "bg-green-500" : "bg-red-500";

                    // Display venue details
                    echo "
                    <div class='bg-white rounded-lg shadow-md overflow-hidden'>
                      <div class='relative'>
                        <img src='{$image}' alt='{$venue["name"]}' class='w-full h-48 object-cover'/>
                        <div class='absolute top-2 left-2 px-2 py-1 text-white text-xs font-semibold rounded $status_color'>
                          " . ucfirst($status) . "
                        </div>
                        <div class='absolute top-2 right-2 bg-white p-1 rounded-full text-xs font-semibold'>
                          <i class='fas fa-heart'></i>
                        </div>
                      </div>
                      <div class='p-4'>
                        <div class='flex items-center mb-2'>
                          <i class='fas fa-map-marker-alt text-gray-500 mr-2'></i>
                          <h2 class='text-lg font-bold'>{$venue["name"]}</h2>
                        </div>
                        <p class='text-gray-600 text-sm mb-2'>Outdoor / Public</p>
                        <div class='flex items-center mb-2'>
                          <span class='bg-red-100 text-red-500 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded'>{$location}</span>
                        </div>
                        <div class='flex flex-wrap gap-2 mb-4'>
                          <span class='bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded'>Latitude: $lat</span>
                          <span class='bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded'>Longitude: $lng</span>
                          <span class='bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded'>Price: $price</span>
                          <span class='bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded'>Location: $location</span>
                          <span class='bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded'>Capacity: $capacity</span>
                         
                        </div>
                        <div class='flex items-center justify-between'>
                          <a href='{$view_link}' class='bg-orange-500 text-white text-sm font-semibold px-4 py-2 rounded'>
                            View Court <i class='fas fa-arrow-right ml-1'></i>
                          </a>
                          <a href='venue_details.php?id={$venue["id"]}' class='bg-blue-500 text-white text-sm font-semibold px-4 py-2 rounded'>
                            Book Now <i class='fas fa-calendar-check ml-1'></i>
                          </a>
                          <div class='flex items-center'>
                            <i class='fas fa-star text-yellow-500 mr-1'></i>
                            <span class='text-gray-800 text-sm font-semibold'>{$rating}</span>
                            <span class='text-gray-600 text-sm ml-1'>{$ratings_count} ratings</span>
                          </div>
                        </div>
                      </div>
                    </div>";
                }
            } else {
                echo "<p class='text-gray-600'>No venues available.</p>";
            }

            $stmt->close();
        }

        $conn->close();
      ?>
    </div>
  </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
