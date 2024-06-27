<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as postStudentMarks.php

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

// Handle form submission to post student marks
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $marks = $_POST['marks'];

    // Validate marks data (assuming marks are passed as an array)
    // Example: $marks = ['student_id_1' => 85, 'student_id_2' => 92, ...]

    // Prepare SQL statement to update student marks
    $sql_update = "UPDATE students SET marks = ? WHERE student_id = ?";
    $stmt_update = $connection->prepare($sql_update);

    foreach ($marks as $student_id => $mark) {
        $stmt_update->bind_param('ii', $mark, $student_id);
        $stmt_update->execute();
    }

    $message = "Marks updated successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Student Marks</title>
    <style>
        /* CSS styles remain the same as in the previous example */
        /* Ensure to include all necessary CSS styles for header, main content, form, tables, messages, etc. */
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
                <h1>Post Student Marks</h1>
                <?php if (!empty($message)): ?>
                    <p class="message success"><?php echo htmlspecialchars($message); ?></p>
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
                    <div class="field">
                        <label for="marks">Enter Marks:</label>
                        <!-- Assuming marks are entered for each student in a text input -->
                        <?php foreach ($classes as $class): ?>
                            <h2><?php echo htmlspecialchars($class['class_name']); ?></h2>
                            <div>
                                <?php
                                $sql_students = "SELECT student_id, first_name, last_name FROM students WHERE class_id = ?";
                                $stmt_students = $connection->prepare($sql_students);
                                $stmt_students->bind_param('i', $class['class_id']);
                                $stmt_students->execute();
                                $result_students = $stmt_students->get_result();

                                if ($result_students->num_rows > 0) {
                                    while ($row_student = $result_students->fetch_assoc()) {
                                        echo '<div class="student">';
                                        echo '<label>';
                                        echo htmlspecialchars($row_student['first_name'] . ' ' . $row_student['last_name']) . ': ';
                                        echo '<input type="number" name="marks[' . htmlspecialchars($row_student['student_id']) . ']" required>';
                                        echo '</label>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p>No students found for this class.</p>';
                                }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit">Post Marks</button>
                </form>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
