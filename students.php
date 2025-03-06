<?php
include 'includes/header.php';
check_login('Admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $conn->query("INSERT INTO students (first_name, last_name, birthdate, gender, address, phone, email) 
                  VALUES ('$first_name', '$last_name', '$birthdate', '$gender', '$address', '$phone', '$email')");
}
?>
<h2>Students</h2>
<!-- Search Bar -->
<form method="GET" class="mb-3">
    <input type="text" name="name" placeholder="Search by Name" class="form-control d-inline w-25">
    <select name="gender" class="form-select d-inline w-25">
        <option value="">Search by Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
</form>
<!-- Add Student Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add New Student</button>
<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Student</h5></div>
            <div class="modal-body">
                <form method="POST">
                    <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required>
                    <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required>
                    <input type="date" name="birthdate" class="form-control mb-2" required>
                    <select name="gender" class="form-select mb-2" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <textarea name="address" class="form-control mb-2" placeholder="Address" required></textarea>
                    <input type="text" name="phone" class="form-control mb-2" placeholder="Phone" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Student Table -->
<?php
$where = "";
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $name = $conn->real_escape_string($_GET['name']);
    $where .= " WHERE first_name LIKE '%$name%' OR last_name LIKE '%$name%'";
}
if (isset($_GET['gender']) && !empty($_GET['gender'])) {
    $gender = $conn->real_escape_string($_GET['gender']);
    $where .= ($where ? " AND" : " WHERE") . " gender = '$gender'";
}
$students = $conn->query("SELECT * FROM students $where");
echo "<table class='table'><tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>";
while ($student = $students->fetch_assoc()) {
    echo "<tr><td>{$student['id']}</td><td>{$student['first_name']} {$student['last_name']}</td><td>{$student['email']}</td>
          <td><button class='btn btn-warning'>Edit</button> <button class='btn btn-danger'>Delete</button></td></tr>";
}
echo "</table>";
include 'includes/footer.php';
?>