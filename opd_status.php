<?php
session_start();
require 'db_connection.php';

// Fetch departments for dropdown
$query = $conn->query('SELECT DISTINCT department FROM doctors');
$departments = $query->fetchAll(PDO::FETCH_COLUMN);

// Fetch doctors based on selected department
$doctors = [];
if (isset($_GET['department']) && !empty($_GET['department'])) {
    $department = $_GET['department'];
    $query = $conn->prepare('SELECT id, full_name FROM doctors WHERE department = ?');
    $query->execute([$department]);
    $doctors = $query->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch number of patients waiting and calculate average waiting time for the selected doctor
$patientCount = 0;
$avgWaitTime = 0;
if (isset($_GET['doctor']) && !empty($_GET['doctor'])) {
    $doctor_id = $_GET['doctor'];
    
    // Fetch number of patients waiting
    $query = $conn->prepare('
        SELECT COUNT(*) as patient_count
        FROM opd_registrations
        WHERE doctor_assigned = ? AND pat_status = "waiting"
    ');
    $query->execute([$doctor_id]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $patientCount = $result['patient_count'];
    
    // Calculate average waiting time
    $query = $conn->prepare('
        SELECT TIMESTAMPDIFF(MINUTE, o.queue_time, i.consultation_time) as wait_time
        FROM opd_registrations o
        JOIN opd_registrations i ON o.id = i.id
        WHERE o.doctor_assigned = ? AND o.pat_status = "waiting" AND i.pat_status = "in-consultation"
          AND i.consultation_time >= o.queue_time
    ');
    $query->execute([$doctor_id]);
    $waitTimes = $query->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($waitTimes) > 0) {
        $avgWaitTime = array_sum($waitTimes) / count($waitTimes);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPD Status - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
        .form-group {
            margin-bottom: 1rem;
        }
        .tile {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .tile-header {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .tile-body {
            font-size: 1.2rem;
            color: #555;
        }
        .tile-icon {
            font-size: 3rem;
            color: #007bff;
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center"><i class="fas fa-filter"></i> Select Department and Doctor</h4>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo isset($_GET['department']) && $_GET['department'] == $dept ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="doctor">Doctor</label>
                        <select class="form-control" id="doctor" name="doctor" <?php echo empty($doctors) ? 'disabled' : ''; ?> required>
                            <option value="">Select Doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo htmlspecialchars($doctor['id']); ?>" <?php echo isset($_GET['doctor']) && $_GET['doctor'] == $doctor['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($doctor['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Show Status</button>
                </form>
            </div>
        </div>

        <?php if ($patientCount > 0 || isset($_GET['doctor'])): ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center"><i class="fas fa-user-clock"></i> Patients in Queue</h4>
                </div>
                <div class="card-body">
                    <div class="tile animate__animated animate__fadeInUp">
                        <div class="tile-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="tile-header">
                            <?php echo htmlspecialchars($patientCount); ?>
                        </div>
                        <div class="tile-body">
                            Patients Waiting
                        </div>
                    </div>
                    <?php if ($patientCount > 0): ?>
                        <div class="tile animate__animated animate__fadeInUp">
                            <div class="tile-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="tile-header">
                                <?php echo number_format($avgWaitTime, 1); ?> mins
                            </div>
                            <div class="tile-body">
                                Average Waiting Time
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif (isset($_GET['doctor'])): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No patients are currently waiting for the selected doctor.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/wowjs@1.1.3/dist/wow.min.js"></script>
    <script>
        new WOW().init();

        // Update doctors dropdown based on selected department
        $('#department').change(function() {
            var department = $(this).val();
            if (department) {
                $.ajax({
                    url: 'opd_status.php',
                    method: 'GET',
                    data: { department: department },
                    success: function(response) {
                        var doctors = JSON.parse(response);
                        var $doctorSelect = $('#doctor');
                        $doctorSelect.empty();
                        $doctorSelect.append('<option value="">Select Doctor</option>');
                        $.each(doctors, function(index, doctor) {
                            $doctorSelect.append('<option value="' + doctor.id + '">' + doctor.full_name + '</option>');
                        });
                        $doctorSelect.prop('disabled', false);
                    }
                });
            } else {
                $('#doctor').empty().append('<option value="">Select Doctor</option>').prop('disabled', true);
            }
        });
    </script>
</body>
</html>
