<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form inputs
    $venue_id = intval($_POST['venue_id']);
    $venue_name = trim($_POST['venue_name']);
    $description = trim($_POST['description']);
    $owner_name = trim($_POST['owner_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $map_url = trim($_POST['map_url']);
    $facebook = trim($_POST['facebook']);
    $twitter = trim($_POST['twitter'] ?? ''); 
    $instagram = trim($_POST['instagram']);
    $linkedin = trim($_POST['linkedin'] ?? '');

    // File Uploads (Handle single file uploads)
    $header_image = uploadFile('header_image') ?? 'uploads/default_header.jpg';
    $main_image = uploadFile('main_image') ?? 'uploads/default_main.jpg';
    $video_tour = uploadFile('video_tour') ?? null;

    // Gallery Uploads (Multiple Files)
    $gallery_images = uploadMultipleFiles('gallery_images');
    $sidebar_gallery = uploadMultipleFiles('sidebar_gallery');

    // Ensure JSON encoding for gallery images
    $gallery_images_json = $gallery_images ?: json_encode([]);
    $sidebar_gallery_json = $sidebar_gallery ?: json_encode([]);

    // Insert or update venue details
    $sql = "INSERT INTO venue_details 
        (venue_id, venue_name, description, owner_name, phone, email, address, map_url, facebook, twitter, instagram, linkedin, 
        header_image, main_image, video_tour, gallery_images, sidebar_gallery)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        venue_name = VALUES(venue_name), 
        description = VALUES(description), 
        owner_name = VALUES(owner_name), 
        phone = VALUES(phone), 
        email = VALUES(email), 
        address = VALUES(address), 
        map_url = VALUES(map_url), 
        facebook = VALUES(facebook), 
        twitter = VALUES(twitter), 
        linkedin = VALUES(linkedin), 
        instagram = VALUES(instagram),  
        header_image = VALUES(header_image), 
        main_image = VALUES(main_image), 
        video_tour = VALUES(video_tour), 
        gallery_images = VALUES(gallery_images), 
        sidebar_gallery = VALUES(sidebar_gallery)";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("issssssssssssssss", 
            $venue_id, $venue_name, $description, $owner_name, $phone, $email, $address, $map_url, 
            $facebook, $twitter, $instagram, $linkedin, $header_image, $main_image, $video_tour, 
            $gallery_images_json, $sidebar_gallery_json
        );

        // Execute query
        if ($stmt->execute()) {
            header("Location: venue_display.php?venue_id=$venue_id");
            exit();
        } else {
            die("Database Error: " . $stmt->error);
        }
    } else {
        die("SQL Preparation Error: " . $conn->error);
    }
}

// Function to upload a single file
function uploadFile($input_name) {
    if (!empty($_FILES[$input_name]['name']) && $_FILES[$input_name]['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES[$input_name]['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_file)) {
            return $target_file;
        }
    }
    return null;
}

// Function to upload multiple files
function uploadMultipleFiles($input_name) {
    $files = $_FILES[$input_name];
    $uploaded_files = [];

    if (!empty($files['name'][0])) {
        foreach ($files['name'] as $key => $file_name) {
            if ($files['error'][$key] == 0) {
                $target_dir = "uploads/";
                $target_file = $target_dir . time() . "_" . basename($file_name);
                if (move_uploaded_file($files['tmp_name'][$key], $target_file)) {
                    $uploaded_files[] = $target_file;
                }
            }
        }
    }
    return json_encode($uploaded_files);
}
?>
