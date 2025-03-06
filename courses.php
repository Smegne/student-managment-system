<?php
include 'includes/header.php';
check_login('Admin');

// Add new course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $instructor_id = $_POST['instructor_id'];
    $level = $_POST['level'];
    $fee = $_POST['fee'];
    $sql = "INSERT INTO courses (name, description, duration, instructor_id, level, fee) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisd", $name, $description, $duration, $instructor_id, $level, $fee);
    $stmt->execute();
}

// Edit course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_course'])) {
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $instructor_id = $_POST['instructor_id'];
    $level = $_POST['level'];
    $fee = $_POST['fee'];
    $sql = "UPDATE courses SET name = ?, description = ?, duration = ?, instructor_id = ?, level = ?, fee = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssidsi", $name, $description, $duration, $instructor_id, $level, $fee, $course_id);
    $stmt->execute();
}

// Delete course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_course'])) {
    $course_id = $conn->real_escape_string($_POST['course_id']);
    $sql = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
}
?>
<h2>Courses</h2>
<div class="mb-3">
    <input type="text" id="searchCourse" class="form-control w-50" placeholder="Search by Name">
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
                    <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
                    <textarea name="description" class="form-control mb-2" placeholder="Description" required></textarea>
                    <input type="text" name="duration" class="form-control mb-2" placeholder="Duration" required>
                    <select name="instructor_id" class="form-select mb-2" required>
                        <?php
                        $instructors = $conn->query("SELECT id, first_name, last_name FROM users WHERE role = 'Instructor'");
                        while ($inst = $instructors->fetch_assoc()) {
                            echo "<option value='{$inst['id']}'>{$inst['first_name']} {$inst['last_name']}</option>";
                        }
                        ?>
                    </select>
                    <select name="level" class="form-select mb-2" required>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                    <input type="number" name="fee" class="form-control mb-2" placeholder="Fee" required>
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
                    <input type="text" name="name" id="edit_name" class="form-control mb-2" placeholder="Name" required>
                    <textarea name="description" id="edit_description" class="form-control mb-2" placeholder="Description" required></textarea>
                    <input type="text" name="duration" id="edit_duration" class="form-control mb-2" placeholder="Duration" required>
                    <select name="instructor_id" id="edit_instructor_id" class="form-select mb-2" required>
                        <?php
                        $instructors = $conn->query("SELECT id, first_name, last_name FROM users WHERE role = 'Instructor'");
                        while ($inst = $instructors->fetch_assoc()) {
                            echo "<option value='{$inst['id']}'>{$inst['first_name']} {$inst['last_name']}</option>";
                        }
                        ?>
                    </select>
                    <select name="level" id="edit_level" class="form-select mb-2" required>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                    <input type="number" name="fee" id="edit_fee" class="form-control mb-2" placeholder="Fee" required>
                    <button type="submit" name="edit_course" class="btn btn-primary">Update Course</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Course Table -->
<div id="courseResults">
    <?php
    $courses = $conn->query("SELECT c.*, u.first_name, u.last_name FROM courses c LEFT JOIN users u ON c.instructor_id = u.id");
    echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Instructor</th><th>Level</th><th>Actions</th></tr>";
    while ($course = $courses->fetch_assoc()) {
        echo "<tr><td>{$course['id']}</td><td>{$course['name']}</td><td>{$course['first_name']} {$course['last_name']}</td><td>{$course['level']}</td>
              <td><a href='enrollments.php?course_id={$course['id']}' class='btn btn-info'>View Enrolled</a> 
                  <button class='btn btn-warning edit-course' data-id='{$course['id']}' data-name='{$course['name']}' 
                  data-description='{$course['description']}' data-duration='{$course['duration']}' data-instructor='{$course['instructor_id']}' 
                  data-level='{$course['level']}' data-fee='{$course['fee']}' data-bs-toggle='modal' data-bs-target='#editCourseModal'>Edit</button>
                  <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this course?\");'>
                      <input type='hidden' name='course_id' value='{$course['id']}'>
                      <button type='submit' name='delete_course' class='btn btn-danger'>Delete</button>
                  </form></td></tr>";
    }
    echo "</table>";
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
        document.getElementById('edit_description').value = this.dataset.description;
        document.getElementById('edit_duration').value = this.dataset.duration;
        document.getElementById('edit_instructor_id').value = this.dataset.instructor;
        document.getElementById('edit_level').value = this.dataset.level;
        document.getElementById('edit_fee').value = this.dataset.fee;
    });
});
</script>

<?php include 'includes/footer.php'; ?>