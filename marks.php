<?php
include 'includes/header.php';
check_login('Admin');

// Add new mark
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_mark'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $mark = $conn->real_escape_string($_POST['mark']);
    
    $sql = "INSERT INTO marks (student_id, course_id, mark) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $course_id, $mark);
    $stmt->execute();
}

// Edit mark
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_mark'])) {
    $mark_id = $conn->real_escape_string($_POST['mark_id']);
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $mark = $conn->real_escape_string($_POST['mark']);
    
    $sql = "UPDATE marks SET student_id = ?, course_id = ?, mark = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $student_id, $course_id, $mark, $mark_id);
    $stmt->execute();
}

// Delete mark
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_mark'])) {
    $mark_id = $conn->real_escape_string($_POST['mark_id']);
    $sql = "DELETE FROM marks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mark_id);
    $stmt->execute();
}
?>
<h2>Marks</h2>
<div class="mb-3">
    <input type="text" id="searchMark" class="form-control w-50" placeholder="Search by Student Name or Course">
</div>
<!-- Add Marks Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addMarksModal">Add Mark</button>
<!-- Add Marks Modal -->
<div class="modal fade" id="addMarksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Mark</h5></div>
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
                        <label for="mark" class="form-label">Mark (out of 100)</label>
                        <input type="number" name="mark" id="mark" class="form-control" min="0" max="100" required>
                    </div>
                    <button type="submit" name="add_mark" class="btn btn-primary">Add Mark</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Marks Modal -->
<div class="modal fade" id="editMarksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Edit Mark</h5></div>
            <div class="modal-body">
                <form method="POST" id="editMarkForm">
                    <input type="hidden" name="mark_id" id="edit_mark_id">
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
                        <label for="edit_mark" class="form-label">Mark (out of 100)</label>
                        <input type="number" name="mark" id="edit_mark" class="form-control" min="0" max="100" required>
                    </div>
                    <button type="submit" name="edit_mark" class="btn btn-primary">Update Mark</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Marks Table -->
<div id="markResults">
    <?php
    $marks = $conn->query("SELECT m.*, s.id AS student_id, u.first_name, u.last_name, c.name 
                           FROM marks m 
                           JOIN students s ON m.student_id = s.id 
                           JOIN users u ON s.user_id = u.id 
                           JOIN courses c ON m.course_id = c.id");
    echo "<table class='table'><tr><th>Student</th><th>Course</th><th>Mark</th><th>Status</th><th>Actions</th></tr>";
    while ($mark = $marks->fetch_assoc()) {
        echo "<tr><td>{$mark['first_name']} {$mark['last_name']}</td><td>{$mark['name']}</td><td>{$mark['mark']}</td><td>{$mark['status']}</td>
              <td><button class='btn btn-warning edit-mark' data-id='{$mark['id']}' data-student='{$mark['student_id']}' 
                  data-course='{$mark['course_id']}' data-mark='{$mark['mark']}' data-bs-toggle='modal' data-bs-target='#editMarksModal'>Edit</button> 
                  <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this mark?\");'>
                      <input type='hidden' name='mark_id' value='{$mark['id']}'>
                      <button type='submit' name='delete_mark' class='btn btn-danger'>Delete</button>
                  </form></td></tr>";
    }
    echo "</table>";
    ?>
</div>

<script>
document.getElementById('searchMark').addEventListener('input', function() {
    const query = this.value;
    fetch(`search_admin.php?section=marks&query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('markResults').innerHTML = data;
        });
});
document.querySelectorAll('.edit-mark').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_mark_id').value = this.dataset.id;
        document.getElementById('edit_student_id').value = this.dataset.student;
        document.getElementById('edit_course_id').value = this.dataset.course;
        document.getElementById('edit_mark').value = this.dataset.mark;
    });
});
</script>

<?php include 'includes/footer.php'; ?>