<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

// Handle AJAX request for doctors
if (isset($_GET['department'])) {
    $department = $_GET['department'];
    $query = $conn->prepare('SELECT doctor_id, name FROM doctors WHERE department = ?');
    $query->execute([$department]);
    $doctors = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($doctors);
    exit();
}

// Fetch departments
$query = $conn->query('SELECT DISTINCT department FROM doctors');
$departments = $query->fetchAll(PDO::FETCH_COLUMN);

// Fetch doctors for initial load
$query = $conn->query('SELECT id, full_name FROM doctors');
$doctors = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle patient registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_patient'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];
    $password = hash('sha256', $_POST['password']);
    $department = $_POST['department'];
    $doctor_assigned = $_POST['doctor_assigned'];
    $assign_bed = isset($_POST['assign_bed']) ? true : false;
    $category = $_POST['category'];

    // Check if a bed needs to be assigned
    $bed_id = null;
    if ($assign_bed) {
        $query = $conn->query('SELECT bed_id FROM beds WHERE status = "Available" LIMIT 1');
        $bed = $query->fetch(PDO::FETCH_ASSOC);
        if ($bed) {
            $bed_id = $bed['bed_id'];

            // Update the bed status to "Occupied"
            $updateBedQuery = $conn->prepare('UPDATE beds SET status = "Occupied" WHERE bed_id = ?');
            $updateBedQuery->execute([$bed_id]);
        } else {
            $message = "No available beds to assign.";
        }
    }

    // Insert patient data
    $insertPatientQuery = $conn->prepare('
        INSERT INTO patients (name, age, gender, email, mobile_number, password, department, doctor_assigned, bed_id, category)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $insertPatientQuery->execute([$name, $age, $gender, $email, $mobile_number, $password, $department, $doctor_assigned, $bed_id, $category]);

    $message = "Patient added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient - Hospital Management System</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Add New Patient</h4>
            </div>
            <div class="card-body">
                <?php if (isset($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
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
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select class="form-control" id="department" name="department" required>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="doctor_assigned">Doctor Assigned</label>
                        <select class="form-control" id="doctor_assigned" name="doctor_assigned">
                            <option value="">None</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo htmlspecialchars($doctor['id']); ?>"><?php echo htmlspecialchars($doctor['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="Inpatient">Inpatient</option>
                            <option value="Outpatient">Outpatient</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="assign_bed" name="assign_bed">
                            <label class="form-check-label" for="assign_bed">Assign Bed</label>
                        </div>
                    </div>
                    <button type="submit" name="add_patient" class="btn btn-primary btn-block">Add Patient</button>
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
                        url: 'add_patient.php',
                        method: 'GET',
                        data: { department: department },
                        success: function(response) {
                            var doctors = JSON.parse(response);
                            var $doctorSelect = $('#doctor_assigned');
                            $doctorSelect.empty();
                            $doctorSelect.append('<option value="">None</option>');
                            $.each(doctors, function(index, doctor) {
                                $doctorSelect.append('<option value="' + doctor.doctor_id + '">' + doctor.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#doctor_assigned').empty().append('<option value="">None</option>');
                }
            });
        });
    </script>
</body>
</html>
