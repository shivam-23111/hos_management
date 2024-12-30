<?php
require 'db_connection.php';

// Fetch doctors from the database
$query = $conn->query('SELECT * FROM doctors');
$doctors = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Doctors - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
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
    <div class="container mt-4">
        <h2 class="mb-4">Doctors List</h2>
        <table class="table table-hover table-bordered" id="doctorsTable">
            <thead class="thead-dark">
                <tr>
                    <th onclick="sortTable(0)">Doctor ID <i class="fas fa-sort"></i></th>
                    <th onclick="sortTable(1)">Full Name <i class="fas fa-sort"></i></th>
                    <th onclick="sortTable(2)">Email <i class="fas fa-sort"></i></th>
                    <th onclick="sortTable(3)">Department <i class="fas fa-sort"></i></th>
                    <th onclick="sortTable(4)">Category <i class="fas fa-sort"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($doctor['id']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['department']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['category']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        function sortTable(columnIndex) {
            var table = document.getElementById("doctorsTable");
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
