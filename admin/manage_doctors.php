<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $doctorId = $_POST['doctor_id'];

    if ($action == 'delete') {
        $stmt = $conn->prepare('DELETE FROM doctors WHERE id = ?');
        $stmt->execute([$doctorId]);
    } elseif ($action == 'update') {
        $department = $_POST['department'];
        $category = $_POST['category'];

        $stmt = $conn->prepare('UPDATE doctors SET department = ?, category = ? WHERE id = ?');
        $stmt->execute([$department, $category, $doctorId]);
    }

    header('Location: manage_doctors.php');
    exit();
}

// Fetch doctors from the database
$query = $conn->query('SELECT * FROM doctors');
$doctors = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Manage Doctors</h2>
        <table class="table table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Doctor ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Category</th>
                    <th>Action</th>
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
                    <td>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateDoctorModal" data-id="<?php echo $doctor['id']; ?>" data-department="<?php echo $doctor['department']; ?>" data-category="<?php echo $doctor['category']; ?>">Update</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Update Doctor Modal -->
    <div class="modal fade" id="updateDoctorModal" tabindex="-1" aria-labelledby="updateDoctorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateDoctorModalLabel">Update Doctor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="doctor_id" id="updateDoctorId">
                        <div class="form-group">
                            <label for="updateDepartment">Department</label>
                            <select class="form-control" id="updateDepartment" name="department" required>
                                <option value="Cardiology">Cardiology</option>
                                <option value="Neurology">Neurology</option>
                                <option value="Orthopedics">Orthopedics</option>
                                <option value="Pediatrics">Pediatrics</option>
                                <!-- Add more departments as needed -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="updateCategory">Category</label>
                            <select class="form-control" id="updateCategory" name="category" required>
                                <option value="Surgeon">Surgeon</option>
                                <option value="Physician">Physician</option>
                                <option value="Consultant">Consultant</option>
                                <!-- Add more categories as needed -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="action" value="update">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#updateDoctorModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var doctorId = button.data('id');
            var department = button.data('department');
            var category = button.data('category');

            var modal = $(this);
            modal.find('#updateDoctorId').val(doctorId);
            modal.find('#updateDepartment').val(department);
            modal.find('#updateCategory').val(category);
        });
    </script>
</body>
</html>
