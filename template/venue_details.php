<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate Venue ID
$venue_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($venue_id <= 0) {
    echo "<script>alert('Invalid Venue ID!'); window.location.href='venue_list.php';</script>";
    exit();
}

// Fetch venue details securely
$query = "SELECT * FROM venues WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();
$venue = $result->fetch_assoc();
$stmt->close();

if (!$venue) {
    echo "<script>alert('Venue not found!'); window.location.href='venue_list.php';</script>";
    exit();
}

// Handle booking submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $event_name = trim($_POST['event_name']);
    $event_date = trim($_POST['event_date']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $full_name = trim($_POST['full_name']);
    $contact_number = trim($_POST['contact_number']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $num_attendees = intval($_POST['num_attendees']);
    $payment_method = trim($_POST['payment_method']);
    $shared_booking = isset($_POST['shared_booking']) ? 1 : 0;
    $total_cost = isset($venue['price']) ? floatval($venue['price']) : 0;
    $id_photo = null;

    // Validate required fields
    if (empty($event_name) || empty($event_date) || empty($start_time) || empty($end_time) || empty($full_name) || empty($contact_number) || empty($email) || $num_attendees <= 0) {
        echo "<script>alert('Please fill all required fields.');</script>";
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
        exit();
    }

    // Validate phone number (basic check)
    if (!preg_match("/^[0-9]{10,15}$/", $contact_number)) {
        echo "<script>alert('Invalid phone number format.');</script>";
        exit();
    }

    // Upload ID Photo
    if (!empty($_FILES['id_photo']['name']) && $_FILES['id_photo']['error'] === 0) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES['id_photo']['name'], PATHINFO_EXTENSION));
        $file_size = $_FILES['id_photo']['size'];

        if (!in_array($file_ext, $allowed_types)) {
            echo "<script>alert('Only JPG, JPEG, and PNG files are allowed.');</script>";
            exit();
        }

        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            echo "<script>alert('File size must be less than 2MB.');</script>";
            exit();
        }

        $file_name = time() . "_" . uniqid() . "." . $file_ext;
        $id_photo = $target_dir . $file_name;

        if (!move_uploaded_file($_FILES["id_photo"]["tmp_name"], $id_photo)) {
            echo "<script>alert('Error uploading ID Photo.');</script>";
            exit();
        }
    }

    // Insert Booking
    $insertQuery = "INSERT INTO bookings 
    (venue_id, event_name, event_date, start_time, end_time, full_name, contact_number, email, num_attendees, total_cost, payment_method, shared_booking, id_photo) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($insertQuery);
if (!$stmt) {
    error_log("Query preparation failed: " . $conn->error);
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("issssssssdsss", 
    $venue_id, $event_name, $event_date, $start_time, $end_time, 
    $full_name, $contact_number, $email, $num_attendees, 
    $total_cost, $payment_method, $shared_booking, $id_photo
);

if ($stmt->execute()) {
    echo "<script>
        alert('Booking Successful!');
        window.location.href = 'manage_bookings.php';
    </script>";
    exit();
} else {
    error_log("Booking Insert Error: " . $stmt->error);
    echo "<script>alert('Error processing booking. Please check logs.');</script>";
}

$stmt->close();
}

// Set default venue logo
$venue['logo'] = "/venue_locator/images/logo.png";
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Web Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  
    <style>
      .mx-auto {
    margin-left: auto;
    margin-right: auto;
    margin-top: 20px;

}

.p-8 {
    padding: 2rem;
    height: 400px;
}
.absolute.inset-0.bg-black.bg-opacity-50.flex.flex-col.justify-center.items-start.p-8 {
    font-size: 20px;
}

.h-96 {
    height: 26rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
    margin-top: 20px;

}
.bg-white.p-4.shadow-md.\32 024 {
    height: 25%;
    margin-top: 20%;
}
.bg-white.p-4.mb-4.shadow-md.\32 022 {
    height: 20%;
    margin-top: 20%;
}
.bg-white.p-4.mb-4.shadow-md.text-center.\32 021 {
    height: 20%;
    margin-top: 20%;
}

#lightbox-img {
    width: 50%;
}
.w-12 {
    width: 9rem;
    padding: 5px;
}
.h-12 {
    height: 7%;
}
.space-x-2 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(0.5rem* var(--tw-space-x-reverse));
    margin-left: calc(0.rem* calc(1 - var(--tw-space-x-reverse)));
}
.flex.flex-wrap.space-x-2 {
    margin-left: 10px;
}
.img.w-12.h-12.cursor-pointer {
    border-radius: 50px;
}


.container {
            max-width: 1100px;
            margin: auto;
            padding-left: 10px;
            padding-right: 10px;
        }

        .form-container {
    padding: 30px;
    border-radius: 8px;
    background: #000;
    margin-bottom: 10px;
    backdrop-filter: blur(7px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
    width: 1000px;
    margin-top: 20%;
    margin-left: -70%;
}
    
        .venue-details2 img  {
    height: auto;
    border-radius: 8px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
    width: 20%;
    margin-top: 1%;
    margin-left: 10%;
}
        
.venue-details img {
    width: 30%;
    margin-top: 40%;
    margin-left: 10%;
}
        
.venue-details P{
    color: black;
}

        .section-header {
            background: #ff6b81;
            padding: 12px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
        }

        .map-container iframe {
    width: 50%;
    height: 400px;
    border: 0;
    border-radius: 8px;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.4);
    margin-top: 0%;
    margin-left: 6%;
}

        /* Sidebar Styling */
        .sidebar {
            background: #383f45;
            padding: 20px;
            color: white;
            width: 100%; /* Full width for small screens */
            box-sizing: border-box;
        }

        @media (min-width: 768px) {
            .sidebar {
        width: 250px;
        height: 102vh;
        position: fixed;
        overflow-y: auto;
        margin-left: -17%;
        margin-top: -5%;
    }
        }

        .sidebar h3 {
            color: white;
            font-size: calc(1rem + .5vw);
            margin-bottom: 1rem;
            padding-left: 0;
        }

        .sidebar .nav-link {
            color: #fff;
            font-family: "Roboto", "Helvetica Neue", sans-serif;
            font-size: calc(1rem + .2vw);
            font-weight: bold;
            padding: 0.5rem 0;
            display: block;
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        .sidebar .nav-flex-column {
            margin-top: 8rem;
        }

        /* Adjustments for Venue Details */
        .venue-details{
    margin-top: 20px;
    margin-left: -80%;
}
.venue-details2 {
    margin-top: 20%;
    margin-left: -80%;

}

@media (min-width: 768px) {
    .venue-details {
        margin-top: -140%;
        margin-left: -80%;
    }
}
        /* Form Adjustments */
        @media (min-width: 768px) {
            .col-md-8 {
                margin-left: 60%; /* Adjust margin to accommodate sidebar */
            }
        }

        /* Form Control Styling */
        .form-control:focus,
        .btn-primary:hover,
        .btn-secondary:hover {
            border-color: #ff6b81;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 129, 0.2);
        }

        .btn-primary,
        .btn-secondary {
            background-color: #ff6b81;
            border-color: #ff6b81;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
        }

        .btn-secondary {
            background-color: #4a148c;
            border-color: #4a148c;
        }

        label {
            color: #fff;
            font-weight: bold;
            margin-bottom: 6px;
            display: block;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="email"],
        input[type="number"],
        textarea,
        select {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 12px;
            width: 100%;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            backdrop-filter: blur(3px);
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        .form-check-input:checked {
            background-color: #ff6b81;
            border-color: #ff6b81;
        }

        .form-check-label {
            color: #fff;
            font-weight: normal;
            margin-left: 3px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
        }

        .venue-details2 h2 {
    margin-top: -6%;
}
.h1{
    color: black;
    font-weight: bold;
    font-style: normal;
}

    </style>
</head>
<body class="bg-gray-100 text-white">
    <!-- Header -->
    <header class="bg-gray-800 p-4 flex justify-between items-center">
        <div class="flex items-center">
            <img alt="Ventech" class="h-10 w-10" src="<?= $venue['logo'] ?>" width="40" height="40"/>
            <span class="ml-2 text-xl font-bold">primovenues</span>
        </div>
        <nav class="flex space-x-4 relative z-50">
    <a class="hover:underline" href="#">Submit Venue</a>

    <div class="relative group">
        <a class="hover:underline cursor-pointer flex items-center" href="#">
            Explore <i class="fas fa-chevron-down ml-1"></i>
        </a>

        <!-- Dropdown Menu -->
        <div class="absolute hidden group-hover:block bg-white text-black mt-2 py-2 w-48 shadow-lg border border-gray-200 z-50">
            <a href="index.php" class="block px-4 py-2 hover:bg-gray-200">Home</a>
            <a href="list_venues.php" class="block px-4 py-2 hover:bg-gray-200">List Venues</a>
            <a  href="manage_bookings.php" class="block px-4 py-2 hover:bg-gray-200">Bookings</a>
            <a href="find.php" class="block px-4 py-2 hover:bg-gray-200">Find Venues</a>
        </div>
    </div>

    <a href="#" class="hover:underline">Help</a>
    <a href="signin.php" class="hover:underline">Sign In</a>
</nav>

    </header>
    <!-- Main Content -->
    <main class="relative">
    <!-- Display the venue's header image -->
    <img alt="<?= htmlspecialchars($venue['name'] ?? 'Venue Name') ?>" class="w-full h-96 object-cover" src="<?= htmlspecialchars($venue['image'] ?? 'default_image.jpg') ?>" width="1200" height="400"/>

    <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-start p-8">
        <h2><?= htmlspecialchars($venue['name'] ?? 'No Name') ?></h2>
        <p><?= htmlspecialchars($venue['description'] ?? 'No Description') ?></p>
        <h2><?= htmlspecialchars($venue['location'] ?? 'No Location') ?></h2>
        <p><strong>Price:</strong>₱<?= htmlspecialchars(number_format($venue['price'] ?? 0, 2)) ?></p>
        <p><strong>Capacity:</strong> <?= htmlspecialchars($venue['capacity'] ?? 'N/A') ?> people</p>

        <h2>
            <?php 
            // Combine categories if they exist
            $categories = [];
            if (isset($venue['category'])) $categories[] = $venue['category'];
            if (isset($venue['category2'])) $categories[] = $venue['category2'];
            if (isset($venue['category3'])) $categories[] = $venue['category3'];
            echo htmlspecialchars(implode(', ', $categories) ?? 'No Categories');
            ?>
        </h2>

        <div class="mt-4">
            <button class="bg-transparent border border-white py-2 px-4 text-white hover:bg-white hover:text-black">
                Login to bookmark this listing
            </button>
        </div>

        <div class="mt-2 flex items-center">
            <div class="flex space-x-1">
                <?php for ($i = 0; $i < 5; $i++) : ?>
                    <i class="far fa-star"></i>
                <?php endfor; ?>
            </div>
            <span class="ml-2"><?= $venue['reviews'] ?? '0' ?> Reviews</span>
        </div>

        <div class="mt-4 flex space-x-2">
            <button class="bg-blue-600 py-2 px-4 text-white hover:bg-blue-700">Contact</button>
            <button class="bg-yellow-500 py-2 px-4 text-white hover:bg-yellow-600">Write a Review</button>
        </div>
    </div>
</main>


<div class="col-md-8">
    <div class="form-container">
        <h4 class="mb-3">Ventech - Venue Reservation</h4>
        <form method="POST" enctype="multipart/form-data">
            <label>Event Name:</label>
            <input type="text" name="event_name" class="form-control" required>

            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Date of Event:</label>
                    <input type="date" name="event_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Venue:</label>
                    <input type="text" name="venue" class="form-control" value="<?= htmlspecialchars($venue['name']); ?>" style="width: 100%;" required readonly>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Start Time:</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>End Time:</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>

            <label>Full Name:</label>
            <input type="text" name="full_name" class="form-control" required>

            <label>Contact Number:</label>
            <input type="text" name="contact_number" class="form-control" required>

            <label>Email Address:</label>
            <input type="email" name="email" class="form-control" required>

            <label>Number of Attendees:</label>
            <input type="number" name="num_attendees" class="form-control" required>

            <label>Upload ID for Verification:</label>
            <input type="file" name="id_photo" class="form-control" accept="image/*" required>

            <label>Additional Requests:</label>
            <textarea name="requests" class="form-control"></textarea>

            <label>Total Cost:</label>
            <input type="text" class="form-control" value="₱<?= number_format(isset($venue['price']) ? $venue['price'] : 0, 2); ?>" readonly>

            <label>Payment Method:</label>
            <div>
                <input type="radio" name="payment_method" value="Cash" required> Cash
                <input type="radio" name="payment_method" value="Credit/Debit"> Credit/Debit
                <input type="radio" name="payment_method" value="Online"> Online
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="shared_booking" id="shared_booking" value="1">
                <label class="form-check-label" for="shared_booking">
                    Allow shared booking
                </label>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Book Now</button>
        </form>
    </div>
</div>


</body>
</html>