<?php
include 'db_connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $venue_name = $_POST['venue_name'];
    $description = $_POST['description'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $maps_url = $_POST['maps_url'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];

    // Handle image uploads
    $header_photo = uploadFile($_FILES['header_photo']);
    $main_image = uploadFile($_FILES['main_image']);
    $video_tour = uploadFile($_FILES['video_tour']);
    
    // Upload thumbnails
    $thumbnails = [];
    foreach ($_FILES['thumbnails']['name'] as $key => $name) {
        $file = [
            'name' => $_FILES['thumbnails']['name'][$key],
            'tmp_name' => $_FILES['thumbnails']['tmp_name'][$key]
        ];
        $thumbnails[] = uploadFile($file);
    }
    $thumbnails_json = json_encode($thumbnails);

    // Insert into database
    $sql = "INSERT INTO venue_details (venue_name, description, phone, email, address, maps_url, facebook, twitter, instagram, header_photo, main_image, video_tour, thumbnails) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssss", $venue_name, $description, $phone, $email, $address, $maps_url, $facebook, $twitter, $instagram, $header_photo, $main_image, $video_tour, $thumbnails_json);

    if ($stmt->execute()) {
        echo "<script>alert('Venue details saved successfully!'); window.location.href='list_venues.php';</script>";
    } else {
        echo "<script>alert('Error saving venue details.');</script>";
    }

    $stmt->close();
    $conn->close();
}

// Function to handle file upload
function uploadFile($file) {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $filePath = $uploadDir . basename($file["name"]);
    move_uploaded_file($file["tmp_name"], $filePath);
    return $filePath;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Details Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <form method="POST" enctype="multipart/form-data">
            <!-- Header -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <input type="file" name="header_photo" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded mb-4" required>
                <input type="text" name="venue_name" class="w-full p-2 border rounded" placeholder="Enter your venue's name" required>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-camera"></i> Photo Gallery</h2>
                        <div class="mb-4">
                            <input type="file" name="main_image" accept=".jpeg, .jpg, .png" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="flex space-x-2 overflow-x-auto">
                            <input type="file" name="thumbnails[]" accept=".jpeg, .jpg, .png" class="w-24 h-24 p-2 border rounded" multiple required>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded shadow mt-4">
                        <textarea name="description" class="w-full h-96 p-2 border rounded" placeholder="Enter the description" required></textarea>
                    </div>

                    <div class="bg-white p-4 rounded shadow mt-4">
                        <h2 class="text-2xl font-bold text-blue-600 mb-4"><i class="fas fa-video"></i> Video Tour</h2>
                        <input type="file" name="video_tour" accept=".mp4, .mov, .avi, .wmv" class="w-full p-2 border rounded">
                    </div>

                    <div class="bg-white p-4 rounded shadow mt-4">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full">Submit</button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <div class="bg-white p-4 rounded shadow">
                        <input type="text" name="phone" class="w-full p-2 border rounded mb-2" placeholder="Enter your phone number" required>
                        <input type="email" name="email" class="w-full p-2 border rounded mb-2" placeholder="Enter your email" required>
                        <input type="text" name="address" class="w-full p-2 border rounded mb-2" placeholder="Enter your address" required>
                        <input type="url" name="maps_url" class="w-full p-2 border rounded mb-2" placeholder="Enter Google Maps URL">
                    </div>

                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="text-lg font-bold mb-2"><i class="fas fa-share-alt"></i> Social Profiles</h3>
                        <input type="url" name="facebook" class="w-full p-2 border rounded mb-2" placeholder="Enter your Facebook URL">
                        <input type="url" name="twitter" class="w-full p-2 border rounded mb-2" placeholder="Enter your Twitter URL">
                        <input type="url" name="instagram" class="w-full p-2 border rounded mb-2" placeholder="Enter your Instagram URL">
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

