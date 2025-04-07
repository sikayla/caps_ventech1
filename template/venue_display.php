<?php
include 'config.php'; // Database connection

// Get venue ID from the URL
if (isset($_GET['venue_id'])) {
    $venue_id = intval($_GET['venue_id']);

    // Fetch venue details
    $query = "SELECT * FROM venue_details WHERE venue_id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Query Error: " . $conn->error); // Shows MySQL error if prepare() fails
    }

    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $venue = $result->fetch_assoc();

    if (!$venue) {
        die("Venue not found.");
    }
} else {
    die("Invalid request.");
}

// Convert JSON gallery images to array
$gallery_images = json_decode($venue['gallery_images'], true);
$sidebar_images = json_decode($venue['sidebar_gallery'], true);

// Ensure decoding worked
$gallery_images = is_array($gallery_images) ? $gallery_images : [];
$sidebar_images = is_array($sidebar_images) ? $sidebar_images : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($venue['venue_name']); ?> - Venue Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Header -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <img src="<?php echo htmlspecialchars($venue['header_image']); ?>" class="w-full h-64 object-cover rounded mb-4">
            <h2 class="text-xl font-bold"><?php echo htmlspecialchars($venue['venue_name']); ?></h2>
            <p><strong>Venue ID:</strong> <?php echo htmlspecialchars($venue['venue_id']); ?></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Photo Gallery with Slider -->
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-camera"></i> Photo Gallery</h2>
                    <div class="relative">
                        <button id="prevBtn" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded">❮</button>
                        <img id="mainGalleryImage" src="<?php echo htmlspecialchars($gallery_images[0] ?? $venue['main_image']); ?>" class="w-full p-2 border rounded mb-4">
                        <button id="nextBtn" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded">❯</button>
                    </div>
                    <div class="flex space-x-2 overflow-x-auto mt-2">
                        <?php foreach ($gallery_images as $index => $image) : ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" class="thumbnail w-24 h-24 p-2 border rounded cursor-pointer" data-index="<?php echo $index; ?>">
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white p-4 rounded shadow mt-4">
                    <p class="w-full h-96 p-2 border rounded"><?php echo nl2br(htmlspecialchars($venue['description'])); ?></p>
                </div>

                <!-- Video Tour -->
                <div class="bg-white p-4 rounded shadow mt-4">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-video"></i> Video Tour</h2>
                    <?php if (!empty($venue['video_tour'])) : ?>
                        <video controls class="w-full p-2 border rounded">
                            <source src="<?php echo htmlspecialchars($venue['video_tour']); ?>" type="video/mp4">
                        </video>
                    <?php else : ?>
                        <p>No video available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <!-- Contact Information -->
                <div class="bg-white p-4 rounded shadow">
                    <p><strong>Owner:</strong> <?php echo htmlspecialchars($venue['owner_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($venue['phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($venue['email']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($venue['address']); ?></p>
                    <p><strong>Google Maps:</strong> 
                        <a href="<?php echo htmlspecialchars($venue['map_url']); ?>" target="_blank" class="text-blue-600">
                            <i class="fas fa-map-marker-alt"></i> View on Maps
                        </a>
                    </p>
                </div>

                <!-- Sidebar Gallery with Popup -->
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="text-lg font-bold mb-2"><i class="fas fa-images"></i> Sidebar Gallery</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach ($sidebar_images as $image) : ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" class="sidebar-img w-full p-2 border rounded cursor-pointer">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Sidebar Gallery -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center">
        <div class="relative">
            <button id="closeModal" class="absolute top-0 right-0 text-white text-2xl">&times;</button>
            <img id="modalImage" class="max-w-full max-h-screen">
        </div>
    </div>

    <script>
        // Gallery Slider
        let galleryImages = <?php echo json_encode($gallery_images); ?>;
        let currentImageIndex = 0;
        const mainGalleryImage = document.getElementById('mainGalleryImage');
        document.getElementById('prevBtn').addEventListener('click', () => {
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            mainGalleryImage.src = galleryImages[currentImageIndex];
        });
        document.getElementById('nextBtn').addEventListener('click', () => {
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            mainGalleryImage.src = galleryImages[currentImageIndex];
        });
        document.querySelectorAll('.thumbnail').forEach((img, index) => {
            img.addEventListener('click', () => {
                mainGalleryImage.src = galleryImages[index];
                currentImageIndex = index;
            });
        });

        // Sidebar Gallery Modal
        document.querySelectorAll('.sidebar-img').forEach(img => {
            img.addEventListener('click', () => {
                document.getElementById('modalImage').src = img.src;
                document.getElementById('modal').classList.remove('hidden');
            });
        });
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('modal').classList.add('hidden');
        });
    </script>
</body>
</html>