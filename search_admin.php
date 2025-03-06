<?php
include 'includes/config.php';

$section = isset($_GET['section']) ? $conn->real_escape_string($_GET['section']) : '';
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$like_query = "%$query%";

if ($section == 'students') {
    $sql = "SELECT s.*, u.first_name, u.last_name, u.email 
            FROM students s 
            JOIN users u ON s.user_id = u.id 
            WHERE u.role = 'Student' AND (u.first_name LIKE ? OR u.last_name LIKE ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Email</th><th>Gender</th><th>Actions</th></tr>";
    while ($student = $result->fetch_assoc()) {
        echo "<tr><td>{$student['id']}</td><td>{$student['first_name']} {$student['last_name']}</td><td>{$student['email']}</td><td>{$student['gender']}</td>
              <td><button class='btn btn-warning edit-student' data-id='{$student['id']}' 
                  data-birthdate='{$student['birthdate']}' data-gender='{$student['gender']}' 
                  data-address='{$student['address']}' data-phone='{$student['phone']}' 
                  data-bs-toggle='modal' data-bs-target='#editStudentModal'>Edit</button>
                  <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this student?\");'>
                      <input type='hidden' name='student_id' value='{$student['id']}'>
                      <button type='submit' name='delete_student' class='btn btn-danger'>Delete</button>
                  </form></td></tr>";
    }
    echo "</table>";
} elseif ($section == 'courses') {
    $sql = "SELECT c.*, u.first_name, u.last_name 
            FROM courses c 
            LEFT JOIN users u ON c.instructor_id = u.id 
            WHERE c.name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Instructor</th><th>Level</th><th>Actions</th></tr>";
    while ($course = $result->fetch_assoc()) {
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
} elseif ($section == 'enrollments') {
    $sql = "SELECT e.*, s.id AS student_id, u.first_name, u.last_name, c.name 
            FROM enrollments e 
            JOIN students s ON e.student_id = s.id 
            JOIN users u ON s.user_id = u.id 
            JOIN courses c ON e.course_id = c.id 
            WHERE c.name LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<table class='table'><tr><th>Student</th><th>Course</th><th>Date</th><th>Status</th><th>Action</th></tr>";
    while ($enrollment = $result->fetch_assoc()) {
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
} elseif ($section == 'marks') {
    $sql = "SELECT m.*, s.id AS student_id, u.first_name, u.last_name, c.name 
            FROM marks m 
            JOIN students s ON m.student_id = s.id 
            JOIN users u ON s.user_id = u.id 
            JOIN courses c ON m.course_id = c.id 
            WHERE c.name LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<table class='table'><tr><th>Student</th><th>Course</th><th>Mark</th><th>Status</th><th>Actions</th></tr>";
    while ($mark = $result->fetch_assoc()) {
        echo "<tr><td>{$mark['first_name']} {$mark['last_name']}</td><td>{$mark['name']}</td><td>{$mark['mark']}</td><td>{$mark['status']}</td>
              <td><button class='btn btn-warning edit-mark' data-id='{$mark['id']}' data-student='{$mark['student_id']}' 
                  data-course='{$mark['course_id']}' data-mark='{$mark['mark']}' data-bs-toggle='modal' data-bs-target='#editMarksModal'>Edit</button> 
                  <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this mark?\");'>
                      <input type='hidden' name='mark_id' value='{$mark['id']}'>
                      <button type='submit' name='delete_mark' class='btn btn-danger'>Delete</button>
                  </form></td></tr>";
    }
    echo "</table>";
} elseif ($section == 'users') {
    $sql = "SELECT * FROM users WHERE username LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<table class='table'><tr><th>ID</th><th>Username</th><th>Role</th><th>Name</th><th>Email</th><th>Image</th><th>Actions</th></tr>";
    while ($user = $result->fetch_assoc()) {
        $image_path = "assets/images/{$user['profile_image']}";
        echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$user['role']}</td><td>{$user['first_name']} {$user['last_name']}</td>
              <td>{$user['email']}</td><td><img src='$image_path' alt='Profile' width='50' onerror=\"this.src='assets/images/default-avatar.png'\"></td>
              <td><button class='btn btn-warning edit-user' data-id='{$user['id']}' data-username='{$user['username']}' 
                  data-role='{$user['role']}' data-first='{$user['first_name']}' data-last='{$user['last_name']}' 
                  data-email='{$user['email']}' data-bs-toggle='modal' data-bs-target='#editUserModal'>Edit</button>
                  <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>
                      <input type='hidden' name='user_id' value='{$user['id']}'>
                      <button type='submit' name='delete_user' class='btn btn-danger'>Delete</button>
                  </form></td></tr>";
    }
    echo "</table>";
}
?>