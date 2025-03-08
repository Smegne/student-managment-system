<?php
include 'includes/header.php';
check_login('Admin');

// Add new enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_enrollment'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $enrollment_date = $conn->real_escape_string($_POST['enrollment_date']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Check for duplicate enrollment
    if (check_duplicate($conn, 'enrollments', ['student_id' => $student_id, 'course_id' => $course_id])) {
        echo "<div class='alert alert-warning'>This student is already registered in this course!</div>";
    } else {
        $sql = "INSERT INTO enrollments (student_id, course_id, enrollment_date, status) VALUES (?, ?, ?, ?)";
        $result = safe_insert($conn, $sql, "iiss", $student_id, $course_id, $enrollment_date, $status);
        if ($result['success']) {
            echo "<div class='alert alert-success'>Enrollment added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding enrollment: " . $result['error'] . "</div>";
        }
    }
}

// Edit enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_enrollment'])) {
    $enrollment_id = $conn->real_escape_string($_POST['enrollment_id']);
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $enrollment_date = $conn->real_escape_string($_POST['enrollment_date']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE enrollments SET student_id = ?, course_id = ?, enrollment_date = ?, status = ? WHERE id = ?";
    $result = safe_execute($conn, $sql, "iissi", $student_id, $course_id, $enrollment_date, $status, $enrollment_id);
    if ($result['success']) {
        echo "<div class='alert alert-success'>Enrollment updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating enrollment: " . $result['error'] . "</div>";
    }
}

// Delete enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_enrollment'])) {
    $enrollment_id = $conn->real_escape_string($_POST['enrollment_id']);
    $sql = "DELETE FROM enrollments WHERE id = ?";
    $result = safe_execute($conn, $sql, "i", $enrollment_id);
    if ($result['success']) {
        echo "<div class='alert alert-success'>Enrollment deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting enrollment: " . $result['error'] . "</div>";
    }
}
?>
<h2>Enrollments</h2>
<div class="mb-3">
    <input type="text" id="searchEnrollment" class="form-control w-50" placeholder="Search by Course Name or Student Name">
</div>
<!-- Add Enrollment Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">Add New Enrollment</button>
<!-- Add Enrollment Modal -->
<div class="modal fade" id="addEnrollmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Enrollment</h5></div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student Name</label>
                        <select name="student_id" id="student_id" class="form-select" required>
                            <option value="">-- Select a Student --</option>
                            <?php
                            $students = $conn->query("SELECT s.id, u.first_name, u.last_name 
                                                      FROM students s 
                                                      JOIN users u ON s.user_id = u.id 
                                                      WHERE u.role = 'Student'");
                            while ($student = $students->fetch_assoc()) {
                                echo "<option value='{$student['id']}'>{$student['first_name']} {$student['last_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="">-- Select a Course --</option>
                            <?php
                            $courses = $conn->query("SELECT id, name FROM courses");
                            while ($course = $courses->fetch_assoc()) {
                                echo "<option value='{$course['id']}'>{$course['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="enrollment_date" class="form-label">Enrollment Date</label>
                        <input type="date" name="enrollment_date" id="enrollment_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" name="add_enrollment" class="btn btn-primary">Add Enrollment</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Enrollment Modal -->
<div class="modal fade" id="editEnrollmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Edit Enrollment</h5></div>
            <div class="modal-body">
                <form method="POST" id="editEnrollmentForm">
                    <input type="hidden" name="enrollment_id" id="edit_enrollment_id">
                    <div class="mb-3">
                        <label for="edit_student_id" class="form-label">Student Name</label>
                        <select name="student_id" id="edit_student_id" class="form-select" required>
                            <option value="">-- Select a Student --</option>
                            <?php
                            $students = $conn->query("SELECT s.id, u.first_name, u.last_name 
                                                      FROM students s 
                                                      JOIN users u ON s.user_id = u.id 
                                                      WHERE u.role = 'Student'");
                            while ($student = $students->fetch_assoc()) {
                                echo "<option value='{$student['id']}'>{$student['first_name']} {$student['last_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_course_id" class="form-label">Course</label>
                        <select name="course_id" id="edit_course_id" class="form-select" required>
                            <option value="">-- Select a Course --</option>
                            <?php
                            $courses = $conn->query("SELECT id, name FROM courses");
                            while ($course = $courses->fetch_assoc()) {
                                echo "<option value='{$course['id']}'>{$course['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_enrollment_date" class="form-label">Enrollment Date</label>
                        <input type="date" name="enrollment_date" id="edit_enrollment_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" name="edit_enrollment" class="btn btn-primary">Update Enrollment</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Enrollment Table -->
<div id="enrollmentResults">
    <?php
    $enrollments = $conn->query("SELECT e.*, s.id AS student_id, u.first_name, u.last_name, c.name 
                                 FROM enrollments e 
                                 JOIN students s ON e.student_id = s.id 
                                 JOIN users u ON s.user_id = u.id 
                                 JOIN courses c ON e.course_id = c.id");
    echo "<table class='table'><tr><th>Student</th><th>Course</th><th>Date</th><th>Status</th><th>Action</th></tr>";
    while ($enrollment = $enrollments->fetch_assoc()) {
        echo "<tr><td>{$enrollment['first_name']} {$enrollment['last_name']}</td><td>{$enrollment['name']}</td>
              <td>{$enrollment['enrollment_date']}</td><td>{$enrollment['status']}</td>
              <td><button class='btn btn-warning edit-enrollment' data-id='{$enrollment['id']}' data-student='{$enrollment['student_id']}' 
                  data-course='{$enrollment['course_id']}' data-date='{$enrollment['enrollment_date']}' data-status='{$enrollment['status']}' 
                  data-bs-toggle='modal' data-bs-target='#editEnrollmentModal'>Edit</button>
                  <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this enrollment?\");'>
                      <input type='hidden' name='enrollment_id' value='{$enrollment['id']}'>
                      <button type='submit' name='delete_enrollment' class='btn btn-danger'>Delete</button>
                  </form></td></tr>";
    }
    echo "</table>";
    ?>
</div>

<script>
document.getElementById('searchEnrollment').addEventListener('input', function() {
    const query = this.value;
    fetch(`search_admin.php?section=enrollments&query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('enrollmentResults').innerHTML = data;
        });
});
document.querySelectorAll('.edit-enrollment').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_enrollment_id').value = this.dataset.id;
        document.getElementById('edit_student_id').value = this.dataset.student;
        document.getElementById('edit_course_id').value = this.dataset.course;
        document.getElementById('edit_enrollment_date').value = this.dataset.date;
        document.getElementById('edit_status').value = this.dataset.status;
    });
});
</script>

<?php include 'includes/footer.php'; ?>