<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];
    $department = $_POST['department'];
    $doctor_assigned = $_POST['doctor_assigned'];
    $problem = $_POST['problem'];

    // Generate queue number
    $query = $conn->query('SELECT MAX(queue_number) AS max_queue FROM opd_registrations');
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $queue_number = $result['max_queue'] + 1;

    // Insert registration data
    $query = $conn->prepare('
        INSERT INTO opd_registrations (name, age, gender, email, mobile_number, department, problem, doctor_assigned, queue_number)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $query->execute([$name, $age, $gender, $email, $mobile_number, $department, $problem, $doctor_assigned, $queue_number]);

    $message = "You are registered successfully. Your queue number is " . $queue_number;
}

// Fetch departments
$query = $conn->query('SELECT DISTINCT department FROM doctors');
$departments = $query->fetchAll(PDO::FETCH_COLUMN);

// Fetch doctors for a selected department
if (isset($_GET['department'])) {
    $department = $_GET['department'];
    $query = $conn->prepare('SELECT id, full_name FROM doctors WHERE department = ?');
    $query->execute([$department]);
    $doctors = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($doctors);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPD Registration - Hospital Management System</title>
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
        .alert-success {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Register for OPD</h4>
            </div>
            <div class="card-body">
                <?php if (isset($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <!-- Check OPD Status Button -->
                <div class="text-center mb-4">
                    <a href="opd_status.php" class="btn btn-info">Check OPD Status</a>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" class="form-control" id="age" name="age" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="doctor_assigned">Doctor Assigned</label>
                        <select class="form-control" id="doctor_assigned" name="doctor_assigned">
                            <option value="">Select Doctor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="problem">Describe Your Problem in Short</label>
                        <input type="text" class="form-control" id="problem" name="problem">
                    </div>
                    <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#department').change(function() {
                var department = $(this).val();
                if (department) {
                    $.ajax({
                        url: 'opd_registration.php',
                        method: 'GET',
                        data: { department: department },
                        success: function(response) {
                            var doctors = JSON.parse(response);
                            var $doctorSelect = $('#doctor_assigned');
                            $doctorSelect.empty();
                            $doctorSelect.append('<option value="">Select Doctor</option>');
                            $.each(doctors, function(index, doctor) {
                                $doctorSelect.append('<option value="' + doctor.id + '">' + doctor.full_name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#doctor_assigned').empty().append('<option value="">Select Doctor</option>');
                }
            });

            <?php if (isset($message)): ?>
                $('.alert-success').fadeIn().delay(3000).fadeOut();
            <?php endif; ?>
        });
    </script>
</body>
</html>
