<?php
include 'includes/header.php';

if ($_SESSION['role'] == 'Student') {
    // Student Dashboard
    $student_id = $conn->query("SELECT id FROM students WHERE user_id = {$_SESSION['user_id']}")->fetch_assoc()['id'];
    $enrollments = $conn->query("SELECT c.name, e.enrollment_date, e.status 
                                 FROM enrollments e 
                                 JOIN courses c ON e.course_id = c.id 
                                 WHERE e.student_id = $student_id");
    echo "<h2>Your Enrollments</h2>";
    echo "<table class='table'><tr><th>Course</th><th>Date</th><th>Status</th></tr>";
    while ($row = $enrollments->fetch_assoc()) {
        echo "<tr><td>{$row['name']}</td><td>{$row['enrollment_date']}</td><td>{$row['status']}</td></tr>";
    }
    echo "</table>";
} else {
    // Admin/Instructor Dashboard
    $total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
    $total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
    $total_enrollments = $conn->query("SELECT COUNT(*) as count FROM enrollments")->fetch_assoc()['count'];
    ?>
    <h2>Home</h2>
    <div class="row">
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Students</h5><p><?php echo $total_students; ?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Courses</h5><p><?php echo $total_courses; ?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Enrollments</h5><p><?php echo $total_enrollments; ?></p></div></div></div>
    </div>
    <?php
}

include 'includes/footer.php';
?>