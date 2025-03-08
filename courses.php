<?php
include 'includes/header.php';
check_login('Admin');

// Add new course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $instructor_id = $conn->real_escape_string($_POST['instructor_id']);
    $level = $conn->real_escape_string($_POST['level']);
    $description = $conn->real_escape_string($_POST['description']);
    
    if (check_duplicate($conn, 'courses', ['instructor_id' => $instructor_id, 'level' => $level])) {
        echo "<div class='alert alert-warning'>This course already exists</div>";
    } else {
        $sql = "INSERT INTO courses (name, instructor_id, level, description) VALUES (?, ?, ?, ?)";
        $result = safe_insert($conn, $sql, "siss", $name, $instructor_id, $level, $description);
        if ($result['success']) {
            echo "<div class='alert alert-success'>Course added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding course: " . $result['error'] . "</div>";
        }
    }
}

// Edit course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_course'])) {
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $instructor_id = $conn->real_escape_string($_POST['instructor_id']);
    $level = $conn->real_escape_string($_POST['level']);
    $description = $conn->real_escape_string($_POST['description']);
    
    $sql = "UPDATE courses SET name = ?, instructor_id = ?, level = ?, description = ? WHERE id = ?";
    $result = safe_execute($conn, $sql, "sissi", $name, $instructor_id, $level, $description, $course_id);
    if ($result['success']) {
        echo "<div class='alert alert-success'>Course updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating course: " . $result['error'] . "</div>";
    }
}

// Delete course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_course'])) {
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $sql = "DELETE FROM courses WHERE id = ?";
    $result = safe_execute($conn, $sql, "i", $course_id);
    if ($result['success']) {
        echo "<div class='alert alert-success'>Course deleted successfully!</div>";
    } elseif (strpos($result['error'], 'foreign key constraint fails') !== false) {
        echo "<div class='alert alert-warning'>Cannot delete course: It is currently enrolled by students</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting course: " . $result['error'] . "</div>";
    }
}
?>
<h2>Courses</h2>
<div class="mb-3">
    <input type="text" id="searchCourse" class="form-control w-50" placeholder="Search by Course Name or Instructor">
</div>
<!-- Add Course Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add New Course</button>
<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Course</h5></div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Course Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="instructor_id" class="form-label">Instructor</label>
                        <select name="instructor_id" id="instructor_id" class="form-select" required>
                            <option value="">-- Select an Instructor --</option>
                            <?php
                            $instructors = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name 
                                                         FROM users WHERE role = 'Instructor'");
                            while ($instructor = $instructors->fetch_assoc()) {
                                echo "<option value='{$instructor['id']}'>{$instructor['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select name="level" id="level" class="form-select" required>
                            <option value="">-- Select Level --</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Edit Course</h5></div>
            <div class="modal-body">
                <form method="POST" id="editCourseForm">
                    <input type="hidden" name="course_id" id="edit_course_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Course Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_instructor_id" class="form-label">Instructor</label>
                        <select name="instructor_id" id="edit_instructor_id" class="form-select" required>
                            <option value="">-- Select an Instructor --</option>
                            <?php
                            $instructors = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name 
                                                         FROM users WHERE role = 'Instructor'");
                            while ($instructor = $instructors->fetch_assoc()) {
                                echo "<option value='{$instructor['id']}'>{$instructor['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_level" class="form-label">Level</label>
                        <select name="level" id="edit_level" class="form-select" required>
                            <option value="">-- Select Level --</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="edit_course" class="btn btn-primary">Update Course</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Course Table -->
<div id="courseResults">
    <?php
    $courses = $conn->query("SELECT c.id, c.name, c.instructor_id, c.level, c.description, u.first_name, u.last_name 
                             FROM courses c 
                             JOIN users u ON c.instructor_id = u.id");
    if ($courses === false) {
        echo "<p>Error fetching courses: " . $conn->error . "</p>";
    } else {
        echo "<table class='table'><tr><th>Name</th><th>Instructor</th><th>Level</th><th>Description</th><th>Action</th></tr>";
        while ($course = $courses->fetch_assoc()) {
            $instructor_id = isset($course['instructor_id']) ? $course['instructor_id'] : '';
            echo "<tr><td>{$course['name']}</td><td>{$course['first_name']} {$course['last_name']}</td><td>{$course['level']}</td><td>{$course['description']}</td>
                  <td><button class='btn btn-warning edit-course' data-id='{$course['id']}' data-name='{$course['name']}' 
                      data-instructor='$instructor_id' data-level='{$course['level']}' data-description='{$course['description']}' 
                      data-bs-toggle='modal' data-bs-target='#editCourseModal'>Edit</button>
                      <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this course?\");'>
                          <input type='hidden' name='course_id' value='{$course['id']}'>
                          <button type='submit' name='delete_course' class='btn btn-danger'>Delete</button>
                      </form></td></tr>";
        }
        echo "</table>";
    }
    ?>
</div>

<script>
document.getElementById('searchCourse').addEventListener('input', function() {
    const query = this.value;
    fetch(`search_admin.php?section=courses&query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('courseResults').innerHTML = data;
        });
});
document.querySelectorAll('.edit-course').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_course_id').value = this.dataset.id;
        document.getElementById('edit_name').value = this.dataset.name;
        document.getElementById('edit_instructor_id').value = this.dataset.instructor;
        document.getElementById('edit_level').value = this.dataset.level;
        document.getElementById('edit_description').value = this.dataset.description || '';
    });
});
</script>

<?php include 'includes/footer.php'; ?>