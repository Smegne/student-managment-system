<?php
include 'config.php';
check_login();

// Fetch the logged-in user's profile image
$user_id = $_SESSION['user_id'];
$sql = "SELECT profile_image, first_name, last_name, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set profile image path (use default if not set)
$profile_image = $user['profile_image'] ? "assets/images/{$user['profile_image']}" : "assets/images/default-avatar.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .sidebar {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="bg-dark text-white vh-100 sidebar" style="width: 20%;">
            <h4 class="p-3">Dashboard</h4>
            <!-- Profile Image and User Info -->
            <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="profile-img" onerror="this.src='assets/images/default-avatar.png'">
            <p class="mb-2"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></p>
            <p class="mb-4 text-muted"><?php echo $user['role']; ?></p>
            <!-- Navigation -->
            <ul class="nav flex-column w-100">
                <li class="nav-item"><a href="dashboard.php?section=home" class="nav-link text-white">Home</a></li>
                <?php if ($_SESSION['role'] == 'Admin') { ?>
                    <li class="nav-item"><a href="students.php" class="nav-link text-white">Students</a></li>
                    <li class="nav-item"><a href="courses.php" class="nav-link text-white">Courses</a></li>
                    <li class="nav-item"><a href="enrollments.php" class="nav-link text-white">Enrollments</a></li>
                    <li class="nav-item"><a href="marks.php" class="nav-link text-white">Marks</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link text-white">Users</a></li>
                <?php } ?>
                <li class="nav-item"><a href="dashboard.php?section=marks" class="nav-link text-white">Marks</a></li>
                <li class="nav-item"><a href="dashboard.php?section=security" class="nav-link text-white">Security</a></li>
            </ul>
            <div class="mt-auto p-3">
                <a href="logout.php" class="text-white">Logout</a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="bg-white p-4" style="width: 80%;">