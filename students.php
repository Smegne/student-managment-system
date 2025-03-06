<?php
include 'includes/header.php';
check_login('Admin');

// Add new student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $check = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows == 0) {
        $sql = "INSERT INTO students (user_id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } else {
        echo "<div class='alert alert-warning'>This student is already registered.</div>";
    }
}

// Edit student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_student'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    $sql = "UPDATE students SET birthdate = ?, gender = ?, address = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $birthdate, $gender, $address, $phone, $student_id);
    $stmt->execute();
}

// Delete student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_student'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
}
?>
<h2>Students</h2>
<div class="mb-3">
    <input type="text" id="searchStudent" class="form-control w-50" placeholder="Search by Name">
</div>
<!-- Add Student Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add New Student</button>
<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Student</h5></div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select Student</label>
                        <select name="user_id" id="user_id" class="form-select" onchange="fetchUserDetails(this)" required>
                            <option value="">-- Select a Student --</option>
                            <?php
                            $users = $conn->query("SELECT id, first_name, last_name FROM users WHERE role = 'Student'");
                            while ($user = $users->fetch_assoc()) {
                                echo "<option value='{$user['id']}'>{$user['first_name']} {$user['last_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div id="user_details" class="mb-3">
                        <p><strong>Name:</strong> <span id="display_name"></span></p>
                        <p><strong>Email:</strong> <span id="display_email"></span></p>
                    </div>
                    <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Edit Student Details</h5></div>
            <div class="modal-body">
                <form method="POST" id="editStudentForm">
                    <input type="hidden" name="student_id" id="edit_student_id">
                    <input type="date" name="birthdate" id="edit_birthdate" class="form-control mb-2">
                    <select name="gender" id="edit_gender" class="form-select mb-2">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <textarea name="address" id="edit_address" class="form-control mb-2" placeholder="Address"></textarea>
                    <input type="text" name="phone" id="edit_phone" class="form-control mb-2" placeholder="Phone">
                    <button type="submit" name="edit_student" class="btn btn-primary">Update Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Student Table -->
<div id="studentResults">
    <?php
    $students = $conn->query("SELECT s.*, u.first_name, u.last_name, u.email 
                              FROM students s 
                              JOIN users u ON s.user_id = u.id 
                              WHERE u.role = 'Student'");
    echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Email</th><th>Gender</th><th>Actions</th></tr>";
    while ($student = $students->fetch_assoc()) {
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
    ?>
</div>

<script>
function fetchUserDetails(select) {
    const userId = select.value;
    if (userId) {
        fetch(`get_user_details.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('display_name').textContent = `${data.first_name} ${data.last_name}`;
                document.getElementById('display_email').textContent = data.email;
            });
    } else {
        document.getElementById('display_name').textContent = '';
        document.getElementById('display_email').textContent = '';
    }
}
document.getElementById('searchStudent').addEventListener('input', function() {
    const query = this.value;
    fetch(`search_admin.php?section=students&query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('studentResults').innerHTML = data;
        });
});
document.querySelectorAll('.edit-student').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_student_id').value = this.dataset.id;
        document.getElementById('edit_birthdate').value = this.dataset.birthdate || '';
        document.getElementById('edit_gender').value = this.dataset.gender || '';
        document.getElementById('edit_address').value = this.dataset.address || '';
        document.getElementById('edit_phone').value = this.dataset.phone || '';
    });
});
</script>

<?php include 'includes/footer.php'; ?>