<?php 
require '../config/db.php'; // Ensure this points to your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $venue_id = $_POST['venue_id'];
    $venue_name = $_POST['venue_name'];
    $description = $_POST['description'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $maps_url = $_POST['maps_url'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];  // Added LinkedIn
    $youtube = $_POST['youtube'];  // Added YouTube
    $video_tour = $_POST['video_tour'];

    // Handle Image Uploads
    $header_photo = uploadFile($_FILES['header_photo'], "uploads/");
    $main_image = uploadFile($_FILES['main_image'], "uploads/");

    // Handle multiple thumbnails
    $thumbnails = [];
    foreach ($_FILES['thumbnails']['tmp_name'] as $key => $tmp_name) {
        $thumbnails[] = uploadFile(['name' => $_FILES['thumbnails']['name'][$key], 'tmp_name' => $tmp_name], "uploads/");
    }
    $thumbnails_json = json_encode($thumbnails);

    // Check if venue details already exist
    $stmt = $conn->prepare("SELECT id FROM venue_details WHERE venue_id = ?");
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing venue details
        $stmt = $conn->prepare("UPDATE venue_details 
            SET venue_name=?, description=?, phone=?, email=?, address=?, maps_url=?, 
                facebook=?, twitter=?, instagram=?, linkedin=?, youtube=?, 
                header_photo=?, main_image=?, video_tour=?, thumbnails=? 
            WHERE venue_id=?");
        $stmt->bind_param("sssssssssssssssi", 
            $venue_name, $description, $phone, $email, $address, $maps_url, 
            $facebook, $twitter, $instagram, $linkedin, $youtube, 
            $header_photo, $main_image, $video_tour, $thumbnails_json, $venue_id);
    } else {
        // Insert new venue details
        $stmt = $conn->prepare("INSERT INTO venue_details 
            (venue_id, venue_name, description, phone, email, address, maps_url, 
             facebook, twitter, instagram, linkedin, youtube, 
             header_photo, main_image, video_tour, thumbnails) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssssssss", 
            $venue_id, $venue_name, $description, $phone, $email, $address, $maps_url, 
            $facebook, $twitter, $instagram, $linkedin, $youtube, 
            $header_photo, $main_image, $video_tour, $thumbnails_json);
    }

    if ($stmt->execute()) {
        header("Location: venue_info.php?venue_id=" . $venue_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Function to handle file uploads
function uploadFile($file, $uploadDir) {
    if (!empty($file['name'])) {
        $targetFile = $uploadDir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $targetFile);
        return $targetFile;
    }
    return "";
}
?>
