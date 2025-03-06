<?php
include 'includes/header.php';
check_login('Admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $enrollment_date = $_POST['enrollment_date'];
    $status = $_POST['status'];
    $conn->query("INSERT INTO enrollments (student_id, course_id, enrollment_date, status) 
                  VALUES ('$student_id', '$course_id', '$enrollment_date', '$status')");
}
?>
<h2>Enrollments</h2>
<!-- Search Bar -->
<form method="GET" class="mb-3">
    <input type="text" name="course_name" placeholder="Search by Course Name" class="form-control d-inline w-25">
    <select name="status" class="form-select d-inline w-25">
        <option value="">Search by Status</option>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
</form>
<!-- Add Enrollment Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">Add New Enrollment</button>
<!-- Add Enrollment Modal -->
<div class="modal fade" id="addEnrollmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Enrollment</h5></div>
            <div class="modal-body">
                <form method="POST">
                    <select name="student_id" class="form-select mb-2" required>
                        <?php
                        $students = $conn->query("SELECT id, first_name, last_name FROM students");
                        while ($student = $students->fetch_assoc()) {
                            echo "<option value='{$student['id']}'>{$student['first_name']} {$student['last_name']}</option>";
                        }
                        ?>
                    </select>
                    <select name="course_id" class="form-select mb-2" required>
                        <?php
                        $courses = $conn->query("SELECT id, name FROM courses");
                        while ($course = $courses->fetch_assoc()) {
                            echo "<option value='{$course['id']}'>{$course['name']}</option>";
                        }
                        ?>
                    </select>
                    <input type="date" name="enrollment_date" class="form-control mb-2" required>
                    <select name="status" class="form-select mb-2" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Add Enrollment</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Enrollment Table -->
<?php
$where = "";
if (isset($_GET['course_name']) && !empty($_GET['course_name'])) {
    $course_name = $conn->real_escape_string($_GET['course_name']);
    $where .= " WHERE c.name LIKE '%$course_name%'";
}
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $where .= ($where ? " AND" : " WHERE") . " e.status = '$status'";
}
if (isset($_GET['course_id'])) {
    $course_id = $conn->real_escape_string($_GET['course_id']);
    $where .= ($where ? " AND" : " WHERE") . " e.course_id = '$course_id'";
}
$enrollments = $conn->query("SELECT e.*, s.first_name, s.last_name, c.name 
                             FROM enrollments e 
                             JOIN students s ON e.student_id = s.id 
                             JOIN courses c ON e.course_id = c.id $where");
echo "<table class='table'><tr><th>Student</th><th>Course</th><th>Date</th><th>Status</th><th>Action</th></tr>";
while ($enrollment = $enrollments->fetch_assoc()) {
    echo "<tr><td>{$enrollment['first_name']} {$enrollment['last_name']}</td><td>{$enrollment['name']}</td>
          <td>{$enrollment['enrollment_date']}</td><td>{$enrollment['status']}</td>
          <td><button class='btn btn-warning'>Change Status</button></td></tr>";
}
echo "</table>";
include 'includes/footer.php';
?>