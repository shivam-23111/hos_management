<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

// Handle adding new bed
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bed'])) {
    $ward = $_POST['ward'];

    $query = $conn->prepare('INSERT INTO beds (status, ward) VALUES ("Available", ?)');
    $query->execute([$ward]);

    $message = "New bed added successfully!";
}

// Handle updating bed status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $bed_id = $_POST['bed_id'];
    $status = $_POST['status'];

    $query = $conn->prepare('UPDATE beds SET status = ? WHERE bed_id = ?');
    $query->execute([$status, $bed_id]);

    $message = "Bed status updated successfully!";
}

// Fetch all beds
$query = $conn->query('SELECT * FROM beds');
$beds = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Beds - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
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
        .form-control {
            border-radius: 0.25rem;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Add New Bed</h4>
            </div>
            <div class="card-body">
                <?php if (isset($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="ward">Ward</label>
                        <select class="form-control" id="ward" name="ward" required>
                            <option value="Ward 1">Ward 1</option>
                            <option value="Ward 2">Ward 2</option>
                            <option value="Ward 3">Ward 3</option>
                            <option value="Ward 4">Ward 4</option>
                        </select>
                    </div>
                    <button type="submit" name="add_bed" class="btn btn-primary btn-block">Add Bed</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Manage Bed Status</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bedsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Bed ID</th>
                                <th>Ward</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($beds as $bed): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($bed['bed_id']); ?></td>
                                <td><?php echo htmlspecialchars($bed['ward']); ?></td>
                                <td><?php echo htmlspecialchars($bed['status']); ?></td>
                                <td>
                                    <form method="POST" class="form-inline">
                                        <input type="hidden" name="bed_id" value="<?php echo htmlspecialchars($bed['bed_id']); ?>">
                                        <select name="status" class="form-control mr-2">
                                            <option value="Available" <?php echo $bed['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                                            <option value="Occupied" <?php echo $bed['status'] == 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
                                            <option value="Under Maintenance" <?php echo $bed['status'] == 'Under Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-success">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#bedsTable').DataTable({
                "ordering": true, // Enable sorting
                "searching": true // Enable searching
            });
        });
    </script>
</body>
</html>
