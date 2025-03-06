<?php
include 'includes/header.php';
check_login('Instructor'); // Restrict to Instructors only

$instructor_id = $_SESSION['user_id'];
$section = isset($_GET['section']) ? $_GET['section'] : 'courses';
?>

<h2>Instructor Dashboard - <?php echo ucfirst($section); ?></h2>

<?php if ($section == 'courses') { ?>
    <!-- Assigned Courses -->
    <div class="mb-3">
        <input type="text" id="searchCourses" class="form-control w-50" placeholder="Search by Course Name">
    </div>
    <div id="courseResults">
        <?php
        $courses = $conn->query("SELECT * FROM courses WHERE instructor_id = $instructor_id");
        echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Description</th><th>Level</th><th>Actions</th></tr>";
        while ($course = $courses->fetch_assoc()) {
            echo "<tr><td>{$course['id']}</td><td>{$course['name']}</td><td>{$course['description']}</td><td>{$course['level']}</td>
                  <td><a href='instructor_dashboard.php?section=enrollments&course_id={$course['id']}' class='btn btn-info'>View Enrollments</a></td></tr>";
        }
        echo "</table>";
        ?>
    </div>

<?php } elseif ($section == 'enrollments') { ?>
    <!-- Enrollments in Assigned Courses -->
    <?php
    $course_id = isset($_GET['course_id']) ? $conn->real_escape_string($_GET['course_id']) : null;
    if ($course_id) {
        $course_check = $conn->query("SELECT * FROM courses WHERE id = $course_id AND instructor_id = $instructor_id");
        if ($course_check->num_rows == 0) {
            echo "<div class='alert alert-danger'>You do not have access to this course.</div>";
            exit();
        }
    ?>
    <div class="mb-3">
        <input type="text" id="searchEnrollments" class="form-control w-50" placeholder="Search by Student Name">
    </div>
    <div id="enrollmentResults">
        <?php
        $enrollments = $conn->query("SELECT e.*, s.id AS student_id, u.first_name, u.last_name 
                                     FROM enrollments e 
                                     JOIN students s ON e.student_id = s.id 
                                     JOIN users u ON s.user_id = u.id 
                                     WHERE e.course_id = $course_id");
        echo "<table class='table'><tr><th>Student</th><th>Date</th><th>Status</th><th>Actions</th></tr>";
        while ($enrollment = $enrollments->fetch_assoc()) {
            echo "<tr><td>{$enrollment['first_name']} {$enrollment['last_name']}</td><td>{$enrollment['enrollment_date']}</td>
                  <td>{$enrollment['status']}</td>
                  <td><a href='instructor_dashboard.php?section=marks&student_id={$enrollment['student_id']}&course_id={$course_id}' class='btn btn-info'>View Marks</a></td></tr>";
        }
        echo "</table>";
        ?>
    </div>
    <?php } else { ?>
        <p class="text-muted">Please select a course from 'My Courses' to view enrollments.</p>
    <?php } ?>

<?php } elseif ($section == 'marks') { ?>
    <!-- Marks for Assigned Courses -->
    <?php
    $course_id = isset($_GET['course_id']) ? $conn->real_escape_string($_GET['course_id']) : null;
    $student_id = isset($_GET['student_id']) ? $conn->real_escape_string($_GET['student_id']) : null;
    if ($course_id && $student_id) {
        $course_check = $conn->query("SELECT * FROM courses WHERE id = $course_id AND instructor_id = $instructor_id");
        if ($course_check->num_rows == 0) {
            echo "<div class='alert alert-danger'>You do not have access to this course.</div>";
            exit();
        }
    ?>
    <div class="mb-3">
        <input type="text" id="searchMarks" class="form-control w-50" placeholder="Search by Course Name">
    </div>
    <div id="markResults">
        <?php
        $marks = $conn->query("SELECT m.*, c.name 
                               FROM marks m 
                               JOIN courses c ON m.course_id = c.id 
                               WHERE m.student_id = $student_id AND m.course_id = $course_id");
        if ($marks->num_rows > 0) {
            echo "<table class='table'><tr><th>Course</th><th>Mark</th><th>Status</th></tr>";
            while ($mark = $marks->fetch_assoc()) {
                echo "<tr><td>{$mark['name']}</td><td>{$mark['mark']}</td><td>{$mark['status']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='text-muted'>No marks available for this student in this course.</p>";
        }
        ?>
    </div>
    <?php } else { ?>
        <p class="text-muted">Please select a student from 'Enrollments' to view marks.</p>
    <?php } ?>

<?php } ?>

<!-- Search Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchCourses = document.getElementById('searchCourses');
    const searchEnrollments = document.getElementById('searchEnrollments');
    const searchMarks = document.getElementById('searchMarks');

    if (searchCourses) {
        searchCourses.addEventListener('input', function() {
            const query = this.value;
            fetch(`search_instructor.php?section=courses&query=${encodeURIComponent(query)}&instructor_id=<?php echo $instructor_id; ?>`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('courseResults').innerHTML = data;
                });
        });
    }

    if (searchEnrollments) {
        searchEnrollments.addEventListener('input', function() {
            const query = this.value;
            fetch(`search_instructor.php?section=enrollments&query=${encodeURIComponent(query)}&course_id=<?php echo $course_id; ?>`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('enrollmentResults').innerHTML = data;
                });
        });
    }

    if (searchMarks) {
        searchMarks.addEventListener('input', function() {
            const query = this.value;
            fetch(`search_instructor.php?section=marks&query=${encodeURIComponent(query)}&student_id=<?php echo $student_id; ?>&course_id=<?php echo $course_id; ?>`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('markResults').innerHTML = data;
                });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>