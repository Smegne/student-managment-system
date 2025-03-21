

### README.txt

```
# Student Management System

A web-based Student Management System built with PHP, MySQL, and Bootstrap. This system includes an admin dashboard, login page, and features to manage student records, course enrollment, attendance tracking, and user role management (admin, teacher, student). The UI is responsive, user-friendly, and optimized for security and performance.

---

## Features
- **Admin Dashboard**: Displays statistics (e.g., total students, courses), recent activities, and quick access buttons.
- **User Authentication**: Secure login page with role-based access (admin, teacher, student).
- **Student Management**: Add, edit, and delete student records.
- **Course Enrollment**: Assign students to courses.
- **Attendance Tracking**: Record and view student attendance.
- **User Role Management**: Manage permissions for admin, teacher, and student roles.
- **Responsive Design**: Built with Bootstrap for a mobile-friendly interface.
- **Security**: Input validation, prepared statements to prevent SQL injection, and password hashing.

---

## Technologies Used
- **PHP**: Server-side scripting.
- **MySQL**: Database management.
- **Bootstrap**: Front-end framework for responsive design.
- **HTML/CSS/JavaScript**: Structure, styling, and interactivity.

---

## Prerequisites
Before setting up the project, ensure you have the following installed:
1. **XAMPP/WAMP/MAMP**: Local server environment with Apache and MySQL.
2. **Git**: For cloning the repository.
3. **Text Editor**: e.g., VS Code, Sublime Text, or any IDE.

---

## Step-by-Step Setup Instructions

### Step 1: Clone the Repository
1. Open your terminal or command prompt.
2. Navigate to your desired directory:
   ```
   cd /path/to/your/directory
   ```
3. Clone the repository:
   ```
   git clone (https://github.com/Smegne/student-managment-system/)
   ```
4. Navigate into the project folder:
   ```
   cd student-management-system
   ```

### Step 2: Set Up the Database
1. Start your XAMPP/WAMP/MAMP control panel and ensure Apache and MySQL are running.
2. Open your browser and go to `http://localhost/phpmyadmin`.
3. Create a new database:
   - Click "New" in the left sidebar.
   - Name it `student_management` and click "Create".
4. Import the database schema:
   - Click on the `student_management` database.
   - Go to the "Import" tab.
   - Choose the `database.sql` file from the project folder and click "Go".

### Step 3: Configure the Project
1. Open the project folder in your text editor.
2. Locate the `config.php` file in the root directory.
3. Update the database connection details:
   ```php
   <?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root'); // Default XAMPP username
   define('DB_PASSWORD', '');     // Default XAMPP password (empty)
   define('DB_NAME', 'student_management');

   $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

   if (!$conn) {
       die("Connection failed: " . mysqli_connect_error());
   }
   ?>
   ```

### Step 4: Move Files to Server Directory
1. Copy the entire `student-management-system` folder to your server’s root directory:
   - For XAMPP: `C:/xampp/htdocs/`
   - For WAMP: `C:/wamp/www/`
2. Ensure the folder is renamed to something simple (e.g., `sms`) for easy access.

### Step 5: Run the Application
1. Open your browser.
2. Navigate to:
   ```
   http://localhost/sms
   ```
3. You should see the login page.

### Step 6: Default Login Credentials
- **Admin**:
  - Username: `admin`
  - Password: `admin123`
- **Teacher**:
  - Username: `teacher`
  - Password: `teacher123`
- **Student**:
  - Username: `student`
  - Password: `student123`
> **Note**: Change these credentials after your first login for security.

---



---

## Usage
1. **Login**: Use the credentials based on your role (admin, teacher, student).
2. **Admin Dashboard**:
   - View statistics (e.g., total students).
   - Add/Edit/Delete students via the "Students" section.
   - Enroll students in courses under "Courses".
   - Track attendance in the "Attendance" section.
   - Manage user roles in the "Users" section.
3. **instructor**: View assigned students, mark attendance.
4. **Student**: View enrolled courses and attendance records.

---

## Security Features
- **Password Hashing**: Passwords are stored using PHP’s `password_hash()`.
- **Prepared Statements**: Prevents SQL injection.
- **Session Management**: Secure login/logout with session handling.
- **Input Validation**: Sanitizes and validates all user inputs.

---

## Customization
- Modify `assets/css/style.css` to change the UI styling.
- Add new features by creating additional PHP files in the respective role folders (`admin/`, `teacher/`, `student/`).
- Update the database schema in `database.sql` for additional tables or fields.

---

## Troubleshooting
- **Database Connection Error**: Check `config.php` credentials and ensure MySQL is running.
- **Page Not Found**: Verify the project folder is in the correct server directory.
- **Login Issues**: Ensure default credentials match those in `database.sql`.

---

## Contributing
1. Fork the repository.
2. Create a new branch:
   ```
   git checkout -b feature-name
   ```
3. Make your changes and commit:
   ```
   git commit -m "Add feature-name"
   ```
4. Push to your fork:
   ```
   git push origin feature-name
   ```
5. Create a pull request on GitHub.

---

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.

---

## Contact
For questions or suggestions, reach out to [smegndestew2@gmail.com].
```

---

### Notes for Implementation
To make this `README.txt` fully functional, you’ll need to:
1. **Create the Project**: Build the PHP, MySQL, and Bootstrap files as described (e.g., `login.php`, `config.php`, admin dashboard pages).
2. **Database Schema**: Write a `database.sql` file with tables for users, students, courses, attendance, etc.
3. **Push to GitHub**: Initialize a Git repository, add the files, and push them to your GitHub repo.
