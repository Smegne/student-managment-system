<?php
include 'includes/config.php';

$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$section = isset($_GET['section']) ? $conn->real_escape_string($_GET['section']) : 'home';
$student_id = isset($_GET['student_id']) ? $conn->real_escape_string($_GET['student_id']) : '';

if ($section == 'marks' && $student_id) {
    $sql = "SELECT c.name, m.mark, m.status 
            FROM marks m 
            JOIN courses c ON m.course_id = c.id 
            WHERE m.student_id = ? AND c.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_query = "%$query%";
    $stmt->bind_param("is", $student_id, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table'><tr><th>Course Name</th><th>Mark</th><th>Status</th></tr>";
        while ($mark = $result->fetch_assoc()) {
            echo "<tr><td>{$mark['name']}</td><td>{$mark['mark']}%</td><td>{$mark['status']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='text-muted'>No marks found.</p>";
    }
} elseif ($section == 'home' && $student_id) {
    $sql = "SELECT c.name, e.enrollment_date, e.status 
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id 
            WHERE e.student_id = ? AND c.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_query = "%$query%";
    $stmt->bind_param("is", $student_id, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table'><tr><th>Course</th><th>Date</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['name']}</td><td>{$row['enrollment_date']}</td><td>{$row['status']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='text-muted'>No enrollments found.</p>";
    }
}
?>