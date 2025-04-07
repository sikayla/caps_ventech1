<?php
// Include database connection
include '../config/db.php';

if (!isset($_GET['venue_id']) || empty($_GET['venue_id'])) {
    die("<script>alert('Venue ID is missing.'); window.history.back();</script>");
}

$venue_id = intval($_GET['venue_id']); // Get venue ID from URL

// ✅ Fetch venue name for display
$sql = "SELECT name FROM venues WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();
$venue = $result->fetch_assoc();

if (!$venue) {
    die("<script>alert('Venue not found.'); window.history.back();</script>");
}

$venue_name = $venue['name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $description = htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8');
    $owner_name = htmlspecialchars($_POST['owner_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $google_maps_url = htmlspecialchars($_POST['google_maps_url'] ?? '', ENT_QUOTES, 'UTF-8');
    $facebook = htmlspecialchars($_POST['facebook'] ?? '', ENT_QUOTES, 'UTF-8');
    $twitter = htmlspecialchars($_POST['twitter'] ?? '', ENT_QUOTES, 'UTF-8');
    $instagram = htmlspecialchars($_POST['instagram'] ?? '', ENT_QUOTES, 'UTF-8');

    // ✅ Function to handle file uploads safely
    function uploadFile($file, $destinationFolder) {
        if (!empty($file['name']) && $file['error'] == 0) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov'];

            if (!in_array($extension, $allowed_extensions)) {
                return "";
            }

            $target_path = $destinationFolder . uniqid() . "_" . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                return $target_path;
            }
        }
        return "";
    }

    // ✅ Handle file uploads
    $header_photo = uploadFile($_FILES['header_photo'], "uploads/");
    $video_tour = uploadFile($_FILES['video_tour'], "uploads/");
    $gallery_photos = [];

    if (!empty($_FILES['gallery_photos']['name'][0])) {
        foreach ($_FILES['gallery_photos']['name'] as $key => $photo) {
            $gallery_photos[] = uploadFile(
                ['name' => $photo, 'tmp_name' => $_FILES['gallery_photos']['tmp_name'][$key], 'error' => $_FILES['gallery_photos']['error'][$key]],
                "uploads/"
            );
        }
    }

    $gallery_json = json_encode($gallery_photos);

    // ✅ Insert data into venue_details table
    $sql = "INSERT INTO venue_details 
            (id, venue_name, description, phone, email, address, maps_url, 
             facebook, twitter, instagram, header_photo, video_tour, thumbnails) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("<script>alert('Database error: " . $conn->error . "');</script>");
    }

    $stmt->bind_param(
        "issssssssssss",
        $venue_id, $venue_name, $description, $phone, $email, $address,
        $google_maps_url, $facebook, $twitter, $instagram, $header_photo,
        $video_tour, $gallery_json
    );

    if ($stmt->execute()) {
        echo "<script>alert('Venue details saved successfully!'); window.location.href='view_venues.php?id=$venue_id';</script>";
    } else {
        echo "<script>alert('Error saving venue details: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Venue Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="venue_id" value="<?= $venue['id'] ?>">

            <div class="bg-white p-4 rounded shadow mb-4">
                <label class="block mb-2 font-bold">Header Photo</label>
                <input type="file" name="header_photo" class="w-full p-2 border rounded"/>
                <input type="text" class="w-full p-2 border rounded mt-2" value="<?= $venue['venue_name'] ?>" readonly/>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-camera"></i> Photo Gallery</h2>
                        <input type="file" name="gallery_photos[]" multiple class="w-full p-2 border rounded"/>
                    </div>

                    <div class="bg-white p-4 rounded shadow mt-4">
                        <textarea name="description" class="w-full h-96 p-2 border rounded" placeholder="Enter the description here..."></textarea>
                    </div>

                    <div class="bg-white p-4 rounded shadow mt-4">
                        <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-video"></i> Video Tour</h2>
                        <input type="file" name="video_tour" class="w-full p-2 border rounded"/>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white p-4 rounded shadow">
                        <input type="text" name="owner_name" class="w-full p-2 border rounded mb-2" placeholder="Enter your name here..."/>
                        <input type="text" name="phone" class="w-full p-2 border rounded mb-2" placeholder="Enter your phone number here..."/>
                        <input type="email" name="email" class="w-full p-2 border rounded mb-2" placeholder="Enter your email here..."/>
                        <input type="text" name="address" class="w-full p-2 border rounded mb-2" placeholder="Enter your address here..."/>
                        <input type="url" name="google_maps_url" class="w-full p-2 border rounded mb-2" placeholder="Enter Google Maps URL here..."/>
                    </div>

                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="text-lg font-bold mb-2"><i class="fas fa-share-alt"></i> Social Profiles</h3>
                        <input type="url" name="facebook" class="w-full p-2 border rounded mb-2" placeholder="Facebook URL"/>
                        <input type="url" name="twitter" class="w-full p-2 border rounded mb-2" placeholder="Twitter URL"/>
                        <input type="url" name="instagram" class="w-full p-2 border rounded mb-2" placeholder="Instagram URL"/>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Save Venue Details</button>
            </div>
        </form>
    </div>
</body>
</html>

