<?php
include 'includes/config.php';

if (isset($_GET['user_id'])) {
    $user_id = $conn->real_escape_string($_GET['user_id']);
    $sql = "SELECT first_name, last_name, email FROM users WHERE id = ? AND role = 'Student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode($user ?: []);
    exit();
}
?>