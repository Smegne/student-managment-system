<?php
$password = "123456"; // Replace with the actual password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

echo "Hashed Password: " . $hashedPassword;
?>
