<?php
include 'includes/header.php';
check_login('Admin');

// Add new user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($_POST['role']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    
    $profile_image = 'default-avatar.png';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = $_FILES['profile_image']['type'];
        if (in_array($file_type, $allowed_types)) {
            $target_dir = "assets/images/";
            $file_name = basename($_FILES['profile_image']['name']);
            $target_file = $target_dir . time() . "_" . $file_name;
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = basename($target_file);
            }
        }
    }

    $sql = "INSERT INTO users (username, password, role, first_name, last_name, email, profile_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $password, $role, $first_name, $last_name, $email, $profile_image);
    $stmt->execute();
}

// Edit user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    
    $sql = "SELECT profile_image FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_user = $result->fetch_assoc();
    $profile_image = $current_user['profile_image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_type = $_FILES['profile_image']['type'];
        if (in_array($file_type, $allowed_types)) {
            $target_dir = "assets/images/";
            $file_name = basename($_FILES['profile_image']['name']);
            $target_file = $target_dir . time() . "_" . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = basename($target_file);
            } else {
                echo "<div class='alert alert-danger'>Failed to upload new profile image.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid file type. Only JPG and PNG are allowed.</div>";
        }
    }

    $sql = "UPDATE users SET username = ?, role = ?, first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $username, $role, $first_name, $last_name, $email, $profile_image, $user_id);
    $stmt->execute();
}

// Delete user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
?>
<h2>Users</h2>
<div class="mb-3">
    <input type="text" id="searchUser" class="form-control w-50" placeholder="Search by Username or Name">
</div>
<!-- Add User Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add User</h5></div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="Admin">Admin</option>
                            <option value="Instructor">Instructor</option>
                            <option value="Student">Student</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/jpeg,image/png">
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Edit User</h5></div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" id="editUserForm">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="Admin">Admin</option>
                            <option value="Instructor">Instructor</option>
                            <option value="Student">Student</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_profile_image" class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" id="edit_profile_image" class="form-control" accept="image/jpeg,image/png">
                        <small class="form-text text-muted">Leave blank to keep current image (JPG, PNG only).</small>
                    </div>
                    <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- User Table -->
<div id="userResults">
    <?php
    $users = $conn->query("SELECT * FROM users");
    echo "<table class='table'><tr><th>ID</th><th>Username</th><th>Role</th><th>Name</th><th>Email</th><th>Image</th><th>Actions</th></tr>";
    while ($user = $users->fetch_assoc()) {
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
    ?>
</div>

<script>
document.getElementById('searchUser').addEventListener('input', function() {
    const query = this.value;
    fetch(`search_admin.php?section=users&query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('userResults').innerHTML = data;
        });
});
document.querySelectorAll('.edit-user').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_user_id').value = this.dataset.id;
        document.getElementById('edit_username').value = this.dataset.username;
        document.getElementById('edit_role').value = this.dataset.role;
        document.getElementById('edit_first_name').value = this.dataset.first;
        document.getElementById('edit_last_name').value = this.dataset.last;
        document.getElementById('edit_email').value = this.dataset.email;
    });
});
</script>

<?php include 'includes/footer.php'; ?>