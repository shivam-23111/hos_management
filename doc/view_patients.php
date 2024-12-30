<?php
session_start();
require 'db_connection.php';

// Check if the doctor is logged in
if (!isset($_SESSION['doctor_logged_in']) || !$_SESSION['doctor_logged_in']) {
    header('Location: doctor_login.php');
    exit();
}

$doctor_id = $_SESSION['doctor_id']; // Assuming you have stored doctor_id in session during login

// Fetch patients registered for the logged-in doctor
$query = $conn->prepare("SELECT * FROM opd_registrations WHERE doctor_assigned = ?");
$query->execute([$doctor_id]);
$patients = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patients - Doctor Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
</head>
<body>
   

    <div class="container mt-5">
        <h3 class="text-center">Patients Registered for You</h3>
        <table id="patientsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Department</th>
                    <th>Queue Number</th>
                    <th>Problem</th>
                    <th>Registration Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['age']); ?></td>
                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo htmlspecialchars($patient['mobile_number']); ?></td>
                    <td><?php echo htmlspecialchars($patient['department']); ?></td>
                    <td><?php echo htmlspecialchars($patient['queue_number']); ?></td>
                    <td><?php echo htmlspecialchars($patient['problem']); ?></td>
                    <td><?php echo htmlspecialchars($patient['registration_time']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#patientsTable').DataTable({
                "order": [[6, "asc"]], // Default sorting by Queue Number
                "paging": true,
                "searching": true
            });
        });
    </script>

    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Hospital Management System. All rights reserved.</p>
    </footer>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
