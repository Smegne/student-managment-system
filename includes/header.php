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

$profile_image = $user['profile_image'] ? "assets/images/{$user['profile_image']}" : "assets/images/default-avatar.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System</title>
    <style>
        /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/*Body Styling*/
/* body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color:rgb(39, 191, 64);
    color: #333;
}  */

/* Main Content Area */
.bg-white {
    width: 80%;
    background-color: #fff;
    padding: 20px;
    min-height: 100vh;
}

/* Sidebar with Glassmorphism */
.sidebar.bg-dark {
    width: 20%;
    height: 100vh;
    background: linear-gradient(135deg, rgba(30, 45, 68, 0.9), rgba(45, 65, 95, 0.7)) !important;
    backdrop-filter: blur(10px);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
    color: #fff;
    
    top: 0;
    left: 0;
}

.sidebar h4 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0.75rem 1rem;
}

.sidebar .profile-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease;
}

.sidebar .profile-img:hover {
    transform: scale(1.05);
}

.sidebar p.mb-2 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.sidebar p.mb-4.text-muted {
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    color: #adb5bd !important;
}

/* Bold Sidebar Menu Text */
.sidebar .sidebar-menu .nav-link {
    display: block;
    padding: 12px 20px;
    color: #fff !important;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: bold; /* Bold text */
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
}

.sidebar .sidebar-menu .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #00d4ff !important;
    box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
}

/* Sidebar Separator */
.sidebar-separator {
    width: 80%;
    border: 0;
    height: 1px;
    background: rgba(255, 255, 255, 0.3);
    margin: 20px 0;
}

/* Sidebar Icons at Bottom */
.sidebar-icons {
    display: flex;
    justify-content: center;
    gap: 15px; /* Space between icons */
    flex-wrap: wrap; /* Allow wrapping on small screens */
}

.sidebar-icons a {
    color: #fff !important;
    text-decoration: none;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.sidebar-icons a:hover {
    transform: scale(1.2); /* Slight zoom on hover */
    opacity: 0.8;
}

.sidebar-icons img {
    width: 20px;
    height: 20px;
    filter: brightness(0) invert(1); /* Make icons white */
}

.fas.fa-home {
  font-size: 24px;
  color: #000;
}
.fas.fa-book {
  font-size: 24px;
  color:#000;
}
.fas.fa-graduation-cap {
  font-size: 24px;
  color:#000;
}
.fas.fa-user-check {
  font-size: 24px;
  color:#000; /* green color for success or enrollment */
}
.fas.fa-check {
  font-size: 24px;
  color:#000; /* green for success or approval */
}
.fas.fa-user {
  font-size: 24px;
  color: #000; /* blue color */
}
.fas.fa-lock {
  font-size: 24px;
  color: #000; /* Blue for security */
}






/* Responsive Design */
@media (max-width: 768px) {
    .sidebar.bg-dark {
        width: 100%;
        height: auto;
        position: relative;
    }

    .bg-white {
        width: 100%;
    }

    .sidebar .sidebar-menu {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        display:none;
      
    }

    .sidebar .sidebar-menu .nav-link {
        padding: 10px 15px;
        
    }

    .sidebar-separator {
        width: 90%;
    }

    .sidebar-icons {
        padding: 10px;
        gap: 10px;
    }
}
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="bg-dark text-white vh-100 sidebar" style="width: 20%;">
            <h4 class="p-3">Dashboard</h4>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="profile-img" onerror="this.src='assets/images/default-avatar.png'">
            <p class="mb-2"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></p>
            <p class="mb-4 text-muted"><?php echo $user['role']; ?></p>
            <!-- Navigation Menu (Text Only) -->
            <ul class="nav flex-column w-100 sidebar-menu">
                <?php if ($_SESSION['role'] == 'Admin') { ?>
                    <li class="nav-item"><a href="dashboard.php?section=home" class="nav-link text-white"><i class="fas fa-home"></i>
                    Home</a></li>
                    <li class="nav-item"><a href="students.php" class="nav-link text-white"><i class="fas fa-graduation-cap"></i>
                    Students</a></li>
                    <li class="nav-item"><a href="courses.php" class="nav-link text-white"><i class="fas fa-book"></i>
                    Courses</a></li>
                    <li class="nav-item"><a href="enrollments.php" class="nav-link text-white"><i class="fas fa-user-check"></i>
                    Enrollments</a></li>
                    <li class="nav-item"><a href="marks.php" class="nav-link text-white"><i class="fas fa-check"></i>
                    Marks</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link text-white"><i class="fas fa-user"></i>
                    Users</a></li>
                <?php } elseif ($_SESSION['role'] == 'Instructor') { ?>
                    <li class="nav-item"><a href="instructor_dashboard.php?section=courses" class="nav-link text-white">My Courses</a></li>
                    <li class="nav-item"><a href="instructor_dashboard.php?section=enrollments" class="nav-link text-white">Enrollments</a></li>
                    <li class="nav-item"><a href="instructor_dashboard.php?section=marks" class="nav-link text-white">Marks</a></li>
                <?php } elseif ($_SESSION['role'] == 'Student') { ?>
                    <li class="nav-item"><a href="dashboard.php?section=home" class="nav-link text-white"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a href="dashboard.php?section=marks" class="nav-link text-white"><i class="fas fa-check"></i>Marks</a></li>
                <?php } ?>
                <li class="nav-item"><a href="dashboard.php?section=security" class="nav-link text-white"><i class="fas fa-lock"></i>
                Security</a></li>
            </ul>
            <!-- Separator -->
            <hr class="sidebar-separator">
            <!-- Icons at Bottom -->
            <div class="sidebar-icons mt-auto p-3">
                <?php if ($_SESSION['role'] == 'Admin') { ?>
                    
                <?php } ?>
                <a href="dashboard.php?section=security" class="text-white" title="Security">security</a>
                <a href="logout.php" class="text-white" title="Logout">Logout</a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="bg-white p-4" style="width: 70%;">