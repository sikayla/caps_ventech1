<?php
include 'config.php'; // Database connection

// Get venue ID from the URL
if (isset($_GET['venue_id'])) {
    $venue_id = intval($_GET['venue_id']);

    // Check if venue details exist
    $query = "SELECT * FROM venue_details WHERE venue_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Venue details exist, redirect to venue_display.php
        header("Location: venue_display.php?venue_id=$venue_id");
        exit;
    }

    // Check if venue exists in the venues table
    $query = "SELECT * FROM venues WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    $venue_result = $stmt->get_result();

    if ($venue_result->num_rows === 0) {
        die("Venue not found.");
    }

    // If venue exists but no details are added, show the form
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Details Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Instructions -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <h1 class="text-2xl font-bold mb-2">Instructions</h1>
            <p>Please fill out the form below to customize your venue's page. Upload images, enter your venue's name, contact information, and social media links. Don't forget to add a video tour and allow customers to leave reviews.</p>
        </div>

        <!-- Form Start -->
        <form action="submit_venue_details.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="venue_id" value="<?= htmlspecialchars($venue_id) ?>">

            <!-- Header -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <input type="file" name="header_image" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-4" onchange="previewImage(event, 'header-preview')">
                <img id="header-preview" class="w-full h-48 object-cover rounded shadow mb-4" style="display: none;">
                <input type="text" name="venue_name" class="w-full p-2 border rounded mb-4" placeholder="Enter your venue's name here..." required>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Photo Gallery -->
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-camera"></i> Photo Gallery</h2>
                        <input type="file" name="main_image" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-4" onchange="previewImage(event, 'main-image-preview')">
                        <img id="main-image-preview" class="w-full h-48 object-cover rounded shadow mb-4" style="display: none;">
                        
                        <!-- Thumbnails -->
                        <div class="flex space-x-2 overflow-x-auto">
                        <input type="file" name="gallery_images[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewGallery(event)" required>
    <input type="file" name="gallery_images[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewGallery(event)" required>
    <input type="file" name="gallery_images[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewGallery(event)" required>
    <input type="file" name="gallery_images[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewGallery(event)" required>
    <input type="file" name="gallery_images[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewGallery(event)" required>
    <input type="file" name="gallery_images[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewGallery(event)" required>

    <!-- Gallery Preview -->
    <div id="gallery-preview" class="grid grid-cols-3 gap-2 mt-2"></div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white p-4 rounded shadow mt-4">
                        <textarea name="description" class="w-full h-96 p-2 border rounded" placeholder="Enter the description here..."></textarea>
                    </div>

                    <!-- Video Tour -->
                    <div class="bg-white p-4 rounded shadow mt-4">
                        <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-video"></i> Video Tour</h2>
                        <input type="file" name="video_tour" accept=".mp4, .mov, .avi, .wmv" class="w-full p-2 border rounded">
                    </div>



            </div>
        </form>
    </div>

                <!-- Social Media Links -->
<div class="bg-white p-4 rounded shadow mt-4">
    <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-link"></i> Social Media Links</h2>
    
    <input type="url" name="facebook" class="w-full p-2 border rounded mb-2" placeholder="Facebook URL (e.g., https://facebook.com/yourpage)">
    <input type="url" name="instagram" class="w-full p-2 border rounded mb-2" placeholder="Instagram URL (e.g., https://instagram.com/yourprofile)">
    <input type="url" name="twitter" class="w-full p-2 border rounded mb-2" placeholder="Twitter URL (e.g., https://twitter.com/yourhandle)">
    <input type="url" name="linkedin" class="w-full p-2 border rounded mb-2" placeholder="LinkedIn URL (e.g., https://linkedin.com/in/yourprofile)">
</div>


     <!-- Sidebar -->
     <div class="space-y-4">
                    <!-- Contact Information -->
                    <div class="bg-white p-4 rounded shadow">
                        <input type="text" name="owner_name" class="w-full p-2 border rounded mb-2" placeholder="Enter your name here...">
                        <input type="text" name="phone" class="w-full p-2 border rounded mb-2" placeholder="Enter your phone number here...">
                        <input type="email" name="email" class="w-full p-2 border rounded mb-2" placeholder="Enter your email here...">
                        <input type="text" name="address" class="w-full p-2 border rounded mb-2" placeholder="Enter your address here...">
                        <input type="url" name="map_url" class="w-full p-2 border rounded mb-2" placeholder="Enter Google Maps URL here...">
                    </div>
                </div>

                
             <!-- Sidebar Gallery -->
<div class="bg-white p-4 rounded shadow">
    <h3 class="text-lg font-bold mb-2"><i class="fas fa-images"></i> Sidebar Gallery (Upload 4 Images)</h3>
    
    <!-- 4 File Inputs for Sidebar Images -->
    <input type="file" name="sidebar_gallery[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewSidebarImages(event)" required>
    <input type="file" name="sidebar_gallery[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewSidebarImages(event)" required>
    <input type="file" name="sidebar_gallery[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewSidebarImages(event)" required>
    <input type="file" name="sidebar_gallery[]" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-2" onchange="previewSidebarImages(event)" required>

    <!-- Sidebar Gallery Preview -->
    <div id="sidebar-gallery-preview" class="grid grid-cols-4 gap-2 mt-2"></div>
</div>

    
     <!-- Submit Button -->
     <div class="bg-white p-4 rounded shadow mt-4">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full">Submit</button>
                    </div>
                </div>

    <script>
    function previewImage(event, previewId) {
        let file = event.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = document.getElementById(previewId);
                img.src = e.target.result;
                img.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    }

    function previewGallery(event) {
    let previewContainer = document.getElementById('gallery-preview');
    previewContainer.innerHTML = '';

    let files = event.target.files;
    if (files.length > 6) {
        alert("You can only upload up to 6 images.");
        return;
    }

    for (let i = 0; i < files.length; i++) {
        let reader = new FileReader();
        reader.onload = function(e) {
            let img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add("w-24", "h-24", "rounded", "shadow");
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(files[i]);
    }
}

   function previewSidebarImages(event) {
    let previewContainer = document.getElementById('sidebar-gallery-preview');
    previewContainer.innerHTML = '';

    let files = event.target.files;
    if (files.length > 4) {
        alert("You can only upload up to 4 images.");
        return;
    }

    for (let i = 0; i < files.length; i++) {
        let reader = new FileReader();
        reader.onload = function(e) {
            let img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add("w-24", "h-24", "rounded", "shadow");
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(files[i]);
    }
}


    </script>
</body>
</html>