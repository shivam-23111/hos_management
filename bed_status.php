<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

// Fetch bed status counts
$query = $conn->query('
    SELECT status, COUNT(*) AS count
    FROM beds
    GROUP BY status
');
$statusCounts = $query->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for chart and tiles
$statusMap = [
    'Available' => 0,
    'Occupied' => 0,
    'Under Maintenance' => 0
];
foreach ($statusCounts as $status) {
    if (array_key_exists($status['status'], $statusMap)) {
        $statusMap[$status['status']] = (int) $status['count'];
    }
}

$totalBeds = array_sum($statusMap);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bed Status - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .card-header {
            background-color: #e9ecef;
            border-bottom: 1px solid #ddd;
        }
        .card-header h4 {
            margin: 0;
            font-weight: 500;
        }
        .card-body {
            padding: 20px;
        }
        .tile {
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            color: #fff;
        }
        .tile i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .tile.total-beds {
            background-color: #007bff;
        }
        .tile.occupied-beds {
            background-color: #dc3545;
        }
        .tile.available-beds {
            background-color: #28a745;
        }
        .tile.maintenance-beds {
            background-color: #ffc107;
        }
        #bedStatusChart {
            max-width: 400px; /* Adjust width as needed */
            height: 300px; /* Adjust height as needed */
            margin: auto; /* Center the chart */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Bed Status Overview</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="tile total-beds">
                            <i class="fas fa-bed"></i>
                            <h5>Total Beds</h5>
                            <p id="totalBeds"><?php echo $totalBeds; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="tile occupied-beds">
                            <i class="fas fa-bed"></i>
                            <h5>Occupied Beds</h5>
                            <p id="occupiedBeds"><?php echo $statusMap['Occupied']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="tile available-beds">
                            <i class="fas fa-bed"></i>
                            <h5>Available Beds</h5>
                            <p id="availableBeds"><?php echo $statusMap['Available']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="tile maintenance-beds">
                            <i class="fas fa-wrench"></i>
                            <h5>Maintenance Beds</h5>
                            <p id="maintenanceBeds"><?php echo $statusMap['Under Maintenance']; ?></p>
                        </div>
                    </div>
                </div>
                <canvas id="bedStatusChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
    <script>
        $(document).ready(function() {
            var ctx = document.getElementById('bedStatusChart').getContext('2d');
            var bedStatusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_keys($statusMap)); ?>,
                    datasets: [{
                        label: 'Bed Status',
                        data: <?php echo json_encode(array_values($statusMap)); ?>,
                        backgroundColor: ['#28a745', '#dc3545', '#007bff', '#ffc107'], // Match with tile colors
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    var label = tooltipItem.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += tooltipItem.raw;
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
