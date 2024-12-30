<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if the username already exists
        $stmt = $conn->prepare('SELECT * FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already exists.";
        } else {
            // Hash password using SHA-256
            $hashed_password = hash('sha256', $password);

            // Insert new admin into the database
            $stmt = $conn->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
            if ($stmt->execute([$username, $hashed_password])) {
                $success = "New admin registered successfully.";
            } else {
                $error = "Failed to register new admin.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'assets/nav.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Add New Admin</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php elseif (isset($success)): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Register Admin</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>

    

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('collapsed');
        });
    </script>
    <style>body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background: #343a40;
            color: #fff;
            transition: all 0.3s;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar.collapsed {
            width: 60px;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link .nav-text {
            margin-left: 10px;
            transition: opacity 0.3s;
        }
        .sidebar.collapsed .nav-text {
            opacity: 0;
            display: none;
        }
        .sidebar .nav-link:hover {
            background: #495057;
        }
        .sidebar .nav-link i {
            font-size: 20px;
        }
        .sidebar .collapse {
            padding-left: 20px;
        }
        .sidebar .collapse .nav-link {
            padding-left: 30px;
        }
        .content {
            margin-left: 250px;
            transition: margin-left 0.3s;
        }
        .content.collapsed {
            margin-left: 60px;
        }
        .sidebar.collapsed + .content {
            margin-left: 60px;
        }</style>
</body>
</html>
