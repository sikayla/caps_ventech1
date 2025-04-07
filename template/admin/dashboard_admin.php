<?php
// dashboard_admin.php
session_start();

$configPath = realpath(__DIR__ . "/../config.php");
if ($configPath && file_exists($configPath)) {
    include $configPath;
} else {
    die("Error: Configuration file not found.");
}

if (!isset($conn)) {
    die("Error: Database connection failed.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php"); // Redirect to admin login
    exit();
}

// Fetch total users
$userQuery = $conn->query("SELECT COUNT(*) AS total_users FROM user_admin"); 
$totalUsers = $userQuery ? $userQuery->fetch_assoc()['total_users'] : 0;

// Fetch total bookings
$bookingQuery = $conn->query("SELECT COUNT(*) AS total_bookings FROM bookings");
$totalBookings = $bookingQuery ? $bookingQuery->fetch_assoc()['total_bookings'] : 0;

// Fetch total venues
$venueQuery = $conn->query("SELECT COUNT(*) AS total_venues FROM venues");
$totalVenues = $venueQuery ? $venueQuery->fetch_assoc()['total_venues'] : 0;

// Fetch total revenue from venues (sum of price)
$revenueQuery = $conn->query("SELECT SUM(price) AS total_revenue FROM venues");
$totalRevenue = $revenueQuery ? number_format($revenueQuery->fetch_assoc()['total_revenue'], 2) : "0.00";

// Fetch total clients
$clientQuery = $conn->query("SELECT COUNT(*) AS total_clients FROM client");
$totalClients = $clientQuery ? $clientQuery->fetch_assoc()['total_clients'] : 0;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #0d6efd;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .dashboard-content {
            flex-grow: 1;
            padding: 20px;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 20px;
        }
        .card {
            border-left: 5px solid #0d6efd;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_bookings2.php"><i class="fas fa-calendar-alt"></i> Manage Bookings</a>
    <a href="admin_record.php"><i class="fas fa-users"></i> Users</a>
    <a href="manage_venues.php"><i class="fas fa-map-marker-alt"></i> Manage Venues</a>
    <a href="manage_clients.php"><i class="fas fa-map-marker-alt"></i> Manage Clients</a>
</div>

<div class="dashboard-content">
    <button class="btn btn-danger logout-btn" onclick="window.location.href='admin_logout.php'">Logout</button>
    <h2>Dashboard</h2>

    <div class="row mt-4">
        <!-- Total Users -->
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-user-friends"></i> Total Users</h5>
                    <p class="card-text fs-2"><?= $totalUsers; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-calendar-check"></i> Total Bookings</h5>
                    <p class="card-text fs-2"><?= $totalBookings; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Venues -->
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-map"></i> Total Venues</h5>
                    <p class="card-text fs-2"><?= $totalVenues; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-coins"></i> Total Revenue (₱)</h5>
                    <p class="card-text fs-2">₱<?= $totalRevenue; ?></p>
                </div>
            </div>
        </div>
        <br></br>
        <!-- Total Clients -->

    </div>

    <div class="col-md-3">
    <div class="card text-center p-3">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user"></i> Total Clients</h5>
            <p class="card-text fs-2"><?= $totalClients; ?></p>
        </div>
    </div>
</div>


</div>

</body>
</html>
