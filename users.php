<?php
include 'includes/header.php';
check_login('Admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $conn->query("INSERT INTO users (username, password, role, first_name, last_name, email) 
                  VALUES ('$username', '$password', '$role', '$first_name', '$last_name', '$email')");
}
?>
<h2>Users</h2>
<!-- Add User Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add User</h5></div>
            <div class="modal-body">
                <form method="POST">
                    <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                    <select name="role" class="form-select mb-2" required>
                        <option value="Admin">Admin</option>
                        <option value="Instructor">Instructor</option>
                        <option value="Student">Student</option>
                    </select>
                    <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required>
                    <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- User Table -->
<?php
$users = $conn->query("SELECT * FROM users");
echo "<table class='table'><tr><th>ID</th><th>Username</th><th>Role</th><th>Name</th><th>Email</th><th>Actions</th></tr>";
while ($user = $users->fetch_assoc()) {
    echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$user['role']}</td><td>{$user['first_name']} {$user['last_name']}</td><td>{$user['email']}</td>
          <td><button class='btn btn-warning'>Edit</button> <button class='btn btn-danger'>Delete</button></td></tr>";
}
echo "</table>";
include 'includes/footer.php';
?>