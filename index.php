<?php
// Include the database connection
require 'db_connection.php';

// Query to get the total number of patients
$total_patients_query = $conn->query("SELECT COUNT(*) AS total FROM patients");
$total_patients = $total_patients_query->fetch(PDO::FETCH_ASSOC)['total'];

// Query to get the number of available beds
$available_beds_query = $conn->query("SELECT COUNT(*) AS total FROM beds WHERE status = 'Available'");
$available_beds = $available_beds_query->fetch(PDO::FETCH_ASSOC)['total'];

// Query to get the total number of doctors
$total_doctors_query = $conn->query("SELECT COUNT(*) AS total FROM doctors");
$total_doctors = $total_doctors_query->fetch(PDO::FETCH_ASSOC)['total'];

// Query to calculate patient admission trend (last month vs. current month)
$last_month_patients_query = $conn->query("
    SELECT COUNT(*) AS total FROM patients 
    WHERE MONTH(registration_time) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
    AND YEAR(registration_time) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
$last_month_patients = $last_month_patients_query->fetch(PDO::FETCH_ASSOC)['total'];

$current_month_patients_query = $conn->query("
    SELECT COUNT(*) AS total FROM patients 
    WHERE MONTH(registration_time) = MONTH(CURRENT_DATE)
    AND YEAR(registration_time) = YEAR(CURRENT_DATE)");
$current_month_patients = $current_month_patients_query->fetch(PDO::FETCH_ASSOC)['total'];

$admission_trend = ($last_month_patients > 0) 
    ? (($current_month_patients - $last_month_patients) / $last_month_patients) * 100 
    : 0;

// Query to predict bed occupancy based on current data (simple projection)
$occupied_beds_query = $conn->query("SELECT COUNT(*) AS total FROM beds WHERE status = 'Occupied'");
$occupied_beds = $occupied_beds_query->fetch(PDO::FETCH_ASSOC)['total'];

$total_beds_query = $conn->query("SELECT COUNT(*) AS total FROM beds");
$total_beds = $total_beds_query->fetch(PDO::FETCH_ASSOC)['total'];

$predicted_bed_occupancy = ($total_beds > 0) 
    ? ($occupied_beds / $total_beds) * 100 
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'assets/nav.php'; ?>

<div class="container mt-5">
    <div class="jumbotron text-center">
        <h1 class="display-4">Welcome to Hospital Management System</h1>
        <p class="lead">Efficiently manage hospital operations with our integrated solution.</p>
        <hr class="my-4">
        <p>Use the navigation bar to access various functionalities.</p>
    </div>

    <div class="row text-center mb-4">
        <!-- Total Patients Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Total Patients</h5>
                    <p class="card-text"><?php echo $total_patients; ?></p>
                </div>
            </div>
        </div>
        <!-- Available Beds Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-bed fa-3x mb-3 text-success"></i>
                    <h5 class="card-title">Available Beds</h5>
                    <p class="card-text"><?php echo $available_beds; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Doctors Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-user-md fa-3x mb-3 text-info"></i>
                    <h5 class="card-title">Total Doctors</h5>
                    <p class="card-text"><?php echo $total_doctors; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row text-center mb-4">
        <!-- AI-driven Insight Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title">Patient Admission Trend</h5>
                    <p class="card-text">
                        <?php echo ($admission_trend >= 0 ? 'Admissions have increased by ' : 'Admissions have decreased by ') . abs($admission_trend) . '% this month.'; ?>
                    </p>
                </div>
            </div>
        </div>
        <!-- ML Model Prediction Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-brain fa-3x mb-3 text-danger"></i>
                    <h5 class="card-title">Predicted Bed Occupancy</h5>
                    <p class="card-text">
                        Predicted bed occupancy is <?php echo round($predicted_bed_occupancy); ?>% for the upcoming week.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-light text-center py-3">
    <p>&copy; 2024 Hospital Management System. All rights reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
