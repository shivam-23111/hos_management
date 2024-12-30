<?php
$dsn = 'mysql:host=localhost;dbname=hos_management';
$username = 'root'; // Your database username
$password = 'shiv43312311';     // Your database password

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
