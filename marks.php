<?php
include 'includes/header.php';
check_login('Admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $mark = $_POST['mark'];
    $conn->query("INSERT INTO marks (student_id, course_id, mark) 
                  VALUES ('$student_id', '$course_id', '$mark')");
}
?>
<h2>Marks</h2>
<!-- Add Marks Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addMarksModal">Add Marks</button>
<!-- Add Marks Modal -->
<div class="modal fade" id="addMarksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Marks</h5></div>
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
                    <input type="number" name="mark" class="form-control mb-2" placeholder="Mark (0-100)" min="0" max="100" required>
                    <button type="submit" class="btn btn-primary">Add Marks</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Marks Table -->
<?php
$marks = $conn->query("SELECT m.*, s.first_name, s.last_name, c.name 
                       FROM marks m 
                       JOIN students s ON m.student_id = s.id 
                       JOIN courses c ON m.course_id = c.id");
echo "<table class='table'><tr><th>Student</th><th>Course</th><th>Mark</th><th>Status</th><th>Actions</th></tr>";
while ($mark = $marks->fetch_assoc()) {
    echo "<tr><td>{$mark['first_name']} {$mark['last_name']}</td><td>{$mark['name']}</td><td>{$mark['mark']}</td><td>{$mark['status']}</td>
          <td><button class='btn btn-warning'>Edit</button> <button class='btn btn-danger'>Delete</button></td></tr>";
}
echo "</table>";
include 'includes/footer.php';
?>