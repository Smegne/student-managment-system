<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "student_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function check_login($required_role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    if ($required_role && $_SESSION['role'] !== $required_role) {
        if ($_SESSION['role'] == 'Admin') {
            header("Location: dashboard.php?section=home");
        } elseif ($_SESSION['role'] == 'Instructor') {
            header("Location: instructor_dashboard.php");
        } elseif ($_SESSION['role'] == 'Student') {
            header("Location: dashboard.php?section=home");
        }
        exit();
    }
}
?>