<?php
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

function check_duplicate($conn, $table, $conditions) {
    $where_clause = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($conditions)));
    $sql = "SELECT id FROM $table WHERE $where_clause";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($conditions)); // Default to string; adjust if needed
    $values = array_values($conditions);
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    return $exists;
}

function safe_insert($conn, $sql, $types, ...$params) {
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();
        $error = $success ? null : $conn->error;
        $stmt->close();
        return ['success' => $success, 'error' => $error];
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            return ['success' => false, 'error' => 'Duplicate entry detected'];
        }
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function safe_execute($conn, $sql, $types, ...$params) {
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();
        $error = $success ? null : $conn->error;
        $stmt->close();
        return ['success' => $success, 'error' => $error];
    } catch (mysqli_sql_exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
?>