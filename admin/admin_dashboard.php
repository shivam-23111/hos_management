<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hospital Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
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
        }
    </style>
</head>
<body>
<?php include 'assets/nav.php'; ?>

    <div class="content">
        <div class="container mt-4">
            <h1>Welcome to Admin Dashboard</h1>
            <p>Use the sidebar to navigate through the different sections.</p>
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
</body>
</html>
