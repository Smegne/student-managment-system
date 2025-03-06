<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'student_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check login status and role
function check_login($role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }
    if ($role && $_SESSION['role'] !== $role) {
        header("Location: dashboard.php");
        exit();
    }
}
?>