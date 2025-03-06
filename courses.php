<?php
include 'includes/header.php';
check_login('Admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $instructor_id = $_POST['instructor_id'];
    $level = $_POST['level'];
    $fee = $_POST['fee'];
    $conn->query("INSERT INTO courses (name, description, duration, instructor_id, level, fee) 
                  VALUES ('$name', '$description', '$duration', '$instructor_id', '$level', '$fee')");
}
?>
<h2>Courses</h2>
<!-- Search Bar -->
<form method="GET" class="mb-3">
    <input type="text" name="name" placeholder="Search by Name" class="form-control d-inline w-25">
    <select name="level" class="form-select d-inline w-25">
        <option value="">Search by Level</option>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
</form>
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
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Course Table -->
<?php
$where = "";
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $name = $conn->real_escape_string($_GET['name']);
    $where .= " WHERE name LIKE '%$name%'";
}
if (isset($_GET['level']) && !empty($_GET['level'])) {
    $level = $conn->real_escape_string($_GET['level']);
    $where .= ($where ? " AND" : " WHERE") . " level = '$level'";
}
$courses = $conn->query("SELECT c.*, u.first_name, u.last_name FROM courses c LEFT JOIN users u ON c.instructor_id = u.id $where");
echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Instructor</th><th>Level</th><th>Actions</th></tr>";
while ($course = $courses->fetch_assoc()) {
    echo "<tr><td>{$course['id']}</td><td>{$course['name']}</td><td>{$course['first_name']} {$course['last_name']}</td><td>{$course['level']}</td>
          <td><a href='enrollments.php?course_id={$course['id']}' class='btn btn-info'>View Enrolled</a> 
              <button class='btn btn-warning'>Edit</button> <button class='btn btn-danger'>Delete</button></td></tr>";
}
echo "</table>";
include 'includes/footer.php';
?>