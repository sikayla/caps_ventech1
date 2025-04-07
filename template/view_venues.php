<?php
// Include database connection
include 'db_connection.php';

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<script>alert('Invalid venue ID.'); window.location.href='list_venues.php';</script>");
}

$venue_id = intval($_GET['id']); // Get venue ID from URL

// ✅ Fetch venue details
$sql = "SELECT v.venue_name, vd.*
        FROM venue_details vd
        JOIN venues v ON v.id = vd.venue_id
        WHERE vd.venue_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<script>alert('Venue not found.'); window.location.href='list_venues.php';</script>");
}

$venue = $result->fetch_assoc();
$stmt->close();
$conn->close();

// ✅ Decode gallery images
$gallery_images = json_decode($venue['gallery_photos'], true) ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($venue['venue_name']) ?> - Venue Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2><?= htmlspecialchars($venue['venue_name']) ?></h2>
        </div>
        <div class="card-body">
            
            <!-- Header Photo -->
            <?php if (!empty($venue['header_photo'])): ?>
                <img src="<?= htmlspecialchars($venue['header_photo']) ?>" alt="Header Photo" class="img-fluid rounded mb-4">
            <?php endif; ?>

            <!-- Description -->
            <h4>Description</h4>
            <p><?= nl2br(htmlspecialchars($venue['description'])) ?></p>

            <!-- Contact Information -->
            <h4>Contact Information</h4>
            <p><i class="fas fa-user"></i> <strong>Owner:</strong> <?= htmlspecialchars($venue['owner_name']) ?></p>
            <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <?= htmlspecialchars($venue['phone']) ?></p>
            <p><i class="fas fa-envelope"></i> <strong>Email:</strong> 
                <a href="mailto:<?= htmlspecialchars($venue['email']) ?>" class="text-decoration-none">
                    <?= htmlspecialchars($venue['email']) ?>
                </a>
            </p>
            <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> <?= htmlspecialchars($venue['address']) ?></p>

            <!-- Google Maps Link -->
            <?php if (!empty($venue['google_maps_url'])): ?>
                <h4>Location</h4>
                <a href="<?= htmlspecialchars($venue['google_maps_url']) ?>" target="_blank" class="btn btn-outline-primary">
                    View on Google Maps
                </a>
            <?php endif; ?>

            <!-- Social Media Links -->
            <h4 class="mt-4">Follow Us</h4>
            <div class="d-flex gap-3">
                <?php if (!empty($venue['facebook'])): ?>
                    <a href="<?= htmlspecialchars($venue['facebook']) ?>" target="_blank" class="text-primary">
                        <i class="fab fa-facebook fa-2x"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($venue['twitter'])): ?>
                    <a href="<?= htmlspecialchars($venue['twitter']) ?>" target="_blank" class="text-info">
                        <i class="fab fa-twitter fa-2x"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($venue['instagram'])): ?>
                    <a href="<?= htmlspecialchars($venue['instagram']) ?>" target="_blank" class="text-danger">
                        <i class="fab fa-instagram fa-2x"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Gallery Photos -->
            <?php if (!empty($gallery_images)): ?>
                <h4 class="mt-4">Photo Gallery</h4>
                <div class="row">
                    <?php foreach ($gallery_images as $image): ?>
                        <div class="col-md-3 mb-3">
                            <img src="<?= htmlspecialchars($image) ?>" alt="Gallery Image" class="img-fluid rounded shadow-sm">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Video Tour -->
            <?php if (!empty($venue['video_tour'])): ?>
                <h4 class="mt-4">Video Tour</h4>
                <video controls class="w-100 rounded">
                    <source src="<?= htmlspecialchars($venue['video_tour']) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>

            <!-- Back Button -->
            <div class="mt-4 text-center">
                <a href="list_venues.php" class="btn btn-secondary">Back to Venues</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
