<?php
include 'includes/header.php';

// Determine the section to display (default to 'home')
$section = isset($_GET['section']) ? $_GET['section'] : 'home';

// Handle password change (unchanged)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $section == 'security') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_new_password'];

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (password_verify($old_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            $update_stmt->execute();
            $message = "<div class='alert alert-success'>Password updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>New passwords do not match!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Incorrect old password!</div>";
    }
}
?>

<h2><?php echo ucfirst($section); ?></h2>

<?php if ($section == 'home' && $_SESSION['role'] == 'Admin') { ?>
    <!-- Admin Home Section with Charts -->
    <div class="row mb-4">
        <?php
        $total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
        $total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
        $total_enrollments = $conn->query("SELECT COUNT(*) as count FROM enrollments")->fetch_assoc()['count'];
        ?>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Students</h5><p><?php echo $total_students; ?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Courses</h5><p><?php echo $total_courses; ?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Enrollments</h5><p><?php echo $total_enrollments; ?></p></div></div></div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Enrollment Trends Over Time</h5>
                    <canvas id="enrollmentGraph"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Enrollment by Course</h5>
                    <canvas id="enrollmentPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch data for Line Graph (Enrollments over time)
        fetch('fetch_enrollment_data.php?type=trend')
            .then(response => response.json())
            .then(data => {
                const ctxGraph = document.getElementById('enrollmentGraph').getContext('2d');
                new Chart(ctxGraph, {
                    type: 'line',
                    data: {
                        labels: data.dates, // Array of dates
                        datasets: [{
                            label: 'Total Enrollments',
                            data: data.counts, // Array of enrollment counts
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { title: { display: true, text: 'Date' } },
                            y: { title: { display: true, text: 'Number of Enrollments' }, beginAtZero: true }
                        }
                    }
                });
            });

        // Fetch data for Pie Chart (Enrollments by course)
        fetch('fetch_enrollment_data.php?type=by_course')
            .then(response => response.json())
            .then(data => {
                const ctxPie = document.getElementById('enrollmentPieChart').getContext('2d');
                new Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: data.courses, // Array of course names
                        datasets: [{
                            label: 'Students Enrolled',
                            data: data.counts, // Array of enrollment counts per course
                            backgroundColor: [
                                '#007bff', '#28a745', '#dc3545', '#ffc107', '#6f42c1', '#17a2b8',
                                '#fd7e14', '#6610f2', '#e83e8c', '#20c997'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        let total = tooltipItem.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = ((tooltipItem.raw / total) * 100).toFixed(1);
                                        return `${tooltipItem.label}: ${tooltipItem.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
    });
    </script>
<?php } elseif ($section == 'home') { ?>
    <!-- Student Home Section (unchanged) -->
    <?php
    $total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
    $total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
    $total_enrollments = $conn->query("SELECT COUNT(*) as count FROM enrollments")->fetch_assoc()['count'];
    ?>
    <div class="row">
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Students</h5><p><?php echo $total_students; ?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Courses</h5><p><?php echo $total_courses; ?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Enrollments</h5><p><?php echo $total_enrollments; ?></p></div></div></div>
    </div>
    <?php
    if ($_SESSION['role'] == 'Student') {
        $student_result = $conn->query("SELECT id FROM students WHERE user_id = {$_SESSION['user_id']}");
        if ($student_result && $student_result->num_rows > 0) {
            $student_id = $student_result->fetch_assoc()['id'];
            ?>
            <h3 class="mt-4">Your Enrollments</h3>
            <div id="enrollmentResults">
                <?php
                $enrollments = $conn->query("SELECT c.name, e.enrollment_date, e.status 
                                             FROM enrollments e 
                                             JOIN courses c ON e.course_id = c.id 
                                             WHERE e.student_id = $student_id");
                if ($enrollments->num_rows > 0) {
                    echo "<table class='table'><tr><th>Course</th><th>Date</th><th>Status</th></tr>";
                    while ($row = $enrollments->fetch_assoc()) {
                        echo "<tr><td>{$row['name']}</td><td>{$row['enrollment_date']}</td><td>{$row['status']}</td></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='text-muted'>No enrollments found.</p>";
                }
                ?>
            </div>
            <?php
        }
    }
} elseif ($section == 'marks') {
    // Marks section (unchanged)
    $user_id = $_SESSION['user_id'];
    $student_result = $conn->query("SELECT id FROM students WHERE user_id = $user_id");
    $student_id = $student_result->num_rows > 0 ? $student_result->fetch_assoc()['id'] : null;

    if ($student_id) {
        ?>
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control w-50" placeholder="Search marks by course">
        </div>
        <div id="markResults">
            <?php
            $marks = $conn->query("SELECT c.name, m.mark, m.status 
                                   FROM marks m 
                                   JOIN courses c ON m.course_id = c.id 
                                   WHERE m.student_id = $student_id");
            if ($marks->num_rows > 0) {
                echo "<table class='table'><tr><th>Course Name</th><th>Mark</th><th>Status</th></tr>";
                while ($mark = $marks->fetch_assoc()) {
                    echo "<tr><td>{$mark['name']}</td><td>{$mark['mark']}%</td><td>{$mark['status']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='text-muted'>No marks available yet.</p>";
            }
            ?>
        </div>
        <?php
    } else {
        echo "<p class='text-muted'>No marks available. You are not registered as a student.</p>";
    }
} elseif ($section == 'security') {
    // Security section (unchanged)
    if (isset($message)) echo $message;
    ?>
    <form method="POST" class="w-50">
        <div class="mb-3">
            <label for="old_password" class="form-label">Old Password</label>
            <input type="password" class="form-control" id="old_password" name="old_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_new_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
    <?php
}
?>

<!-- Search Functionality (unchanged) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            const section = '<?php echo $section; ?>';
            const studentId = '<?php echo $student_id ?? ''; ?>';

            fetch(`search_user.php?query=${encodeURIComponent(query)}&section=${section}&student_id=${studentId}`)
                .then(response => response.text())
                .then(data => {
                    const resultsDiv = document.getElementById(section === 'marks' ? 'markResults' : 'enrollmentResults');
                    resultsDiv.innerHTML = data;
                });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>