<?php
require 'db_connect.php'; // Connect to database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["gallery_images"])) {
    $venue_id = $_POST["venue_id"] ?? null;
    $upload_dir = "uploads/gallery/";
    $allowed_types = ["image/jpeg", "image/png", "image/jpg"];
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploaded_files = [];
    foreach ($_FILES["gallery_images"]["tmp_name"] as $key => $tmp_name) {
        $file_name = basename($_FILES["gallery_images"]["name"][$key]);
        $file_type = $_FILES["gallery_images"]["type"][$key];
        $file_path = $upload_dir . uniqid() . "_" . $file_name;

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($tmp_name, $file_path)) {
                $uploaded_files[] = $file_path;
                $stmt = $conn->prepare("INSERT INTO venue_gallery (venue_id, image_path) VALUES (?, ?)");
                $stmt->bind_param("is", $venue_id, $file_path);
                $stmt->execute();
            }
        }
    }

    if (!empty($uploaded_files)) {
        echo json_encode(["status" => "success", "message" => "Images uploaded successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Upload failed."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
