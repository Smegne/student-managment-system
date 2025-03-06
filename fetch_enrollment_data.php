<?php
include 'includes/config.php';

header('Content-Type: application/json');

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type == 'trend') {
    // Fetch enrollments over time (grouped by date)
    $sql = "SELECT DATE(enrollment_date) as date, COUNT(*) as count 
            FROM enrollments 
            GROUP BY DATE(enrollment_date) 
            ORDER BY enrollment_date ASC";
    $result = $conn->query($sql);

    $dates = [];
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
        $counts[] = (int)$row['count'];
    }

    echo json_encode(['dates' => $dates, 'counts' => $counts]);
} elseif ($type == 'by_course') {
    // Fetch enrollments by course
    $sql = "SELECT c.name, COUNT(e.id) as count 
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id 
            GROUP BY c.id, c.name";
    $result = $conn->query($sql);

    $courses = [];
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row['name'];
        $counts[] = (int)$row['count'];
    }

    echo json_encode(['courses' => $courses, 'counts' => $counts]);
} else {
    echo json_encode(['error' => 'Invalid type']);
}
?>