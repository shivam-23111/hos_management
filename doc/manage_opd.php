<?php
session_start();
require 'db_connection.php';

// Get the doctor's ID from the session
$doctor_id = $_SESSION['doctor_id'] ?? null;

if (!$doctor_id) {
    die('Doctor not logged in.');
}

// Fetch patients and their predictions
$query = $conn->prepare("
    SELECT id, name, age, gender, queue_number, pat_status, problem, 
           predicted_arrival_time, predicted_duration, actual_start_time, actual_end_time
    FROM opd_registrations
    WHERE doctor_assigned = ? AND pat_status != 'completed'
    ORDER BY queue_number ASC
");
$query->execute([$doctor_id]);
$patients = $query->fetchAll(PDO::FETCH_ASSOC);

// Update patient status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $patient_id = $_POST['patient_id'];
    $new_status = $_POST['new_status'];

    // Record the timestamp
    $timestamp_column = $new_status == 'in-consultation' ? 'actual_start_time' : 'actual_end_time';
    $timestamp = date('Y-m-d H:i:s');

    $update_query = $conn->prepare("
        UPDATE opd_registrations 
        SET pat_status = ?, $timestamp_column = ? 
        WHERE id = ?
    ");
    $update_query->execute([$new_status, $timestamp, $patient_id]);

    // Redirect to avoid form resubmission
    header("Location: manage_opd.php");
    exit();
}
?>

<!-- HTML section remains mostly the same -->

<td><?php echo htmlspecialchars($patient['predicted_arrival_time']); ?></td>
<td><?php echo htmlspecialchars($patient['predicted_duration']); ?> minutes</td>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage OPD Queue - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
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
        .card-body {
            padding: 20px;
        }
        .fa-user-md {
            color: #007bff;
            margin-right: 10px;
        }
        .fa-user-clock {
            color: #28a745;
        }
        .btn-status {
            margin-right: 5px;
        }
        .table {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-user-md"></i> Manage OPD Queue</h4>
            </div>
            <div class="card-body">
                <table id="queueTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Queue No.</th>
                            <th>Patient Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Problem</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['queue_number']); ?></td>
                                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                <td><?php echo htmlspecialchars($patient['age']); ?></td>
                                <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                                <td><?php echo htmlspecialchars($patient['problem']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $patient['pat_status'] == 'waiting' ? 'primary' : ($patient['pat_status'] == 'in-consultation' ? 'warning' : 'success'); ?>">
                                        <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $patient['pat_status']))); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($patient['pat_status'] == 'waiting'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                                            <input type="hidden" name="new_status" value="in-consultation">
                                            <button type="submit" name="update_status" class="btn btn-warning btn-status">
                                                <i class="fas fa-user-clock"></i> Start Consultation
                                            </button>
                                        </form>
                                    <?php elseif ($patient['pat_status'] == 'in-consultation'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                                            <input type="hidden" name="new_status" value="completed">
                                            <button type="submit" name="update_status" class="btn btn-success btn-status">
                                                <i class="fas fa-user-check"></i> Complete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#queueTable').DataTable({
                "paging": false,
                "info": false,
                "ordering": false,
                "searching": false
            });
        });
    </script>
</body>
</html>
