<?php
include 'includes/config.php';

$section = isset($_GET['section']) ? $conn->real_escape_string($_GET['section']) : '';
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$instructor_id = isset($_GET['instructor_id']) ? $conn->real_escape_string($_GET['instructor_id']) : '';
$course_id = isset($_GET['course_id']) ? $conn->real_escape_string($_GET['course_id']) : '';
$student_id = isset($_GET['student_id']) ? $conn->real_escape_string($_GET['student_id']) : '';
$like_query = "%$query%";

if ($section == 'courses') {
    $sql = "SELECT * FROM courses WHERE instructor_id = ? AND name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $instructor_id, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Description</th><th>Level</th><th>Actions</th></tr>";
    while ($course = $result->fetch_assoc()) {
        echo "<tr><td>{$course['id']}</td><td>{$course['name']}</td><td>{$course['description']}</td><td>{$course['level']}</td>
              <td><a href='instructor_dashboard.php?section=enrollments&course_id={$course['id']}' class='btn btn-info'>View Enrollments</a></td></tr>";
    }
    echo "</table>";
} elseif ($section == 'enrollments' && $course_id) {
    $sql = "SELECT e.*, s.id AS student_id, u.first_name, u.last_name 
            FROM enrollments e 
            JOIN students s ON e.student_id = s.id 
            JOIN users u ON s.user_id = u.id 
            WHERE e.course_id = ? AND CONCAT(u.first_name, ' ', u.last_name) LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $course_id, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<table class='table'><tr><th>Student</th><th>Date</th><th>Status</th><th>Actions</th></tr>";
    while ($enrollment = $result->fetch_assoc()) {
        echo "<tr><td>{$enrollment['first_name']} {$enrollment['last_name']}</td><td>{$enrollment['enrollment_date']}</td>
              <td>{$enrollment['status']}</td>
              <td><a href='instructor_dashboard.php?section=marks&student_id={$enrollment['student_id']}&course_id={$course_id}' class='btn btn-info'>View Marks</a></td></tr>";
    }
    echo "</table>";
} elseif ($section == 'marks' && $course_id && $student_id) {
    $sql = "SELECT m.*, c.name 
            FROM marks m 
            JOIN courses c ON m.course_id = c.id 
            WHERE m.student_id = ? AND m.course_id = ? AND c.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $student_id, $course_id, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<table class='table'><tr><th>Course</th><th>Mark</th><th>Status</th></tr>";
        while ($mark = $result->fetch_assoc()) {
            echo "<tr><td>{$mark['name']}</td><td>{$mark['mark']}</td><td>{$mark['status']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='text-muted'>No marks available for this student in this course.</p>";
    }
}
?>