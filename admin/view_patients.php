<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

// Handle search functionality
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Handle sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Fetch sorted patients
$query = $conn->prepare('
    SELECT p.*, d.full_name AS doctor_name, b.bed_id 
    FROM patients p
    LEFT JOIN doctors d ON p.doctor_assigned = d.id
    LEFT JOIN beds b ON p.bed_id = b.bed_id
    WHERE p.name LIKE ? OR p.email LIKE ? OR p.mobile_number LIKE ? 
    ORDER BY p.' . $sort . ' ' . $sort_order
);
$query->execute([$search, $search, $search]);
$patients = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patients - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
            cursor: pointer;
        }
        .table thead th i {
            margin-left: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">View Patients</h4>
            </div>
            <div class="card-body">
                <form method="GET" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Search..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <table class="table table-hover table-bordered" id="patientsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th onclick="sortTable(0)">Name <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(1)">Age <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(2)">Gender <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(3)">Email <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(4)">Mobile Number <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(5)">Department <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(6)">Doctor Assigned <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(7)">Bed ID <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable(8)">Category <i class="fas fa-sort"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($patients): ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['age']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['department']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['bed_id']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['category']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No patients found</td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        function sortTable(columnIndex) {
            var table = document.getElementById("patientsTable");
            var rows = Array.prototype.slice.call(table.rows, 1);
            var ascending = table.rows[0].cells[columnIndex].getAttribute('data-order') === 'asc';
            rows.sort(function(a, b) {
                var cellA = a.cells[columnIndex].textContent.trim();
                var cellB = b.cells[columnIndex].textContent.trim();
                if (cellA < cellB) return ascending ? -1 : 1;
                if (cellA > cellB) return ascending ? 1 : -1;
                return 0;
            });
            table.tBodies[0].append(...rows);
            table.rows[0].cells[columnIndex].setAttribute('data-order', ascending ? 'desc' : 'asc');
        }
    </script>
</body>
</html>
