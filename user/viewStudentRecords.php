<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as viewStudentRecords.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a tutor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    header("Location: login.php");
    exit;
}

// Instantiating the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Get the tutor ID from the session
$tutor_id = $_SESSION['user_id'];

// Retrieve class information from the database for the logged-in tutor
$sql_classes = "SELECT class_id, class_name FROM class WHERE tutor_id = ?";
$stmt_classes = $connection->prepare($sql_classes);
$stmt_classes->bind_param('i', $tutor_id);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();

$classes = [];
if ($result_classes->num_rows > 0) {
    while ($row_class = $result_classes->fetch_assoc()) {
        $classes[] = $row_class;
    }
} else {
    header("Location: createClass.php");
    exit;
}

// Handle form submission to view student records
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];

    $sql_students = "SELECT student_id, first_name, last_name, email FROM students WHERE class_id = ?";
    $stmt_students = $connection->prepare($sql_students);
    $stmt_students->bind_param('i', $class_id);
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();

    $students = [];
    if ($result_students->num_rows > 0) {
        while ($row_student = $result_students->fetch_assoc()) {
            $students[] = $row_student;
        }
    } else {
        $message = "No students found for the selected class.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Records</title>
    <style>
        
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo">
    </div>
    <div class="main">
        <div class="content">
            <div class="button-group">
                <a href="teacherDashboard.php">Homepage</a>
                <a href="createClass.php">Create Class</a>
                <a href="../logout.php">Logout</a>
            </div>
            <div class="form-container">
                <h1>View Student Records</h1>
                <?php if (!empty($message)): ?>
                    <p class="message error"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="field">
                        <label for="class_id">Select Class:</label>
                        <select id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                    <?php echo htmlspecialchars($class['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">View Student Records</button>
                </form>

                <?php if (!empty($students)): ?>
                    <h2>Student Records</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
