

<?php
include 'config.php';
check_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="bg-dark text-white vh-100" style="width: 20%;">
            <h4 class="p-3">Dashboard</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="dashboard.php" class="nav-link text-white">Home</a></li>
                <?php if ($_SESSION['role'] == 'Admin') { ?>
                    <li class="nav-item"><a href="students.php" class="nav-link text-white">Students</a></li>
                    <li class="nav-item"><a href="courses.php" class="nav-link text-white">Courses</a></li>
                    <li class="nav-item"><a href="enrollments.php" class="nav-link text-white">Enrollments</a></li>
                    <li class="nav-item"><a href="marks.php" class="nav-link text-white">Marks</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link text-white">Users</a></li>
                <?php } ?>
            </ul>
            <div class="position-absolute bottom-0 p-3">
                <p>Welcome <?php echo ($_SESSION['role'] == 'Admin' ? 'Administrator' : 'User') . ': ' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></p>
                <a href="logout.php" class="text-white">Logout</a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="bg-white p-4" style="width: 80%;">