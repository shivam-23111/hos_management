<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

// Fetch OPD registrations
$query = $conn->query('
    SELECT o.*, d.full_name AS doctor_name
    FROM opd_registrations o
    LEFT JOIN doctors d ON o.doctor_assigned = d.id
    ORDER BY o.registration_time DESC
');
$registrations = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPD Registrations - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        .table thead th {
            text-align: center;
        }
        .table tbody td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">OPD Registrations</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Registration ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Department</th>
                            <th>Doctor Assigned</th>
                            <th>Queue Number</th>
                            <th>Registration Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($registrations): ?>
                            <?php foreach ($registrations as $registration): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($registration['id']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['name']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['age']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['email']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['department']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['queue_number']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['registration_time']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No registrations found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
