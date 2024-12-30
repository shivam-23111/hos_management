<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) || !$_SESSION['doctor_logged_in']) {
    header('Location: doctor_login.php');
    exit();
}

require '../db_connection.php';

// Fetch doctor's information from the database
$doctor_id = $_SESSION['doctor_id'];
$query = $conn->prepare('SELECT * FROM doctors WHERE id = ?');
$query->execute([$doctor_id]);
$doctor = $query->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Hospital Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="doctor_dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_patients.php">View Patients</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="appointments.php">Appointments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_schedule.php">Manage Schedule</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Welcome, Dr. <?php echo htmlspecialchars($doctor['full_name']); ?></h2>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">View Patients</h5>
                    <p class="card-text">Check the list of your assigned patients.</p>
                    <a href="view_patients.php" class="btn btn-primary">View Patients</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">OPD</h5>
                    <p class="card-text">manage your opd appointments</p>
                    <a href="manage_opd.php" class="btn btn-primary">Manage Appointments</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Schedule</h5>
                    <p class="card-text">Update your availability and schedule.</p>
                    <a href="manage_schedule.php" class="btn btn-primary">Manage Schedule</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Profile</h5>
                    <p class="card-text">View and update your profile information.</p>
                    <a href="profile.php" class="btn btn-primary">View Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-light text-center py-3 mt-5">
    <p>&copy; 2024 Hospital Management System. All rights reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
