<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as postMarks.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a tutor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    header("Location: login.php");
    exit;
}

// Instantiate the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Handle AJAX request to fetch assignments
if (isset($_GET['action']) && $_GET['action'] === 'getAssignments' && isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];

    // Retrieve assignments for the selected class
    $sql_assignments = "SELECT assignment_id, title FROM assignments WHERE class_id = ?";
    $stmt_assignments = $connection->prepare($sql_assignments);
    $stmt_assignments->bind_param('i', $class_id);
    $stmt_assignments->execute();
    $result_assignments = $stmt_assignments->get_result();

    $assignments = [];
    if ($result_assignments->num_rows > 0) {
        while ($row_assignment = $result_assignments->fetch_assoc()) {
            $assignments[] = $row_assignment;
        }
    }

    // Return assignments as JSON
    header('Content-Type: application/json');
    echo json_encode($assignments);
    exit; // Stop further execution
}

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
    $assignment_id = $_POST['assignment_id'];
    $marks = isset($_POST['marks']) ? $_POST['marks'] : [];

    // Check if the assignment ID exists in the assignments table for the selected class
    $sql_check_assignment = "SELECT assignment_id FROM assignments WHERE assignment_id = ? AND class_id = ?";
    $stmt_check_assignment = $connection->prepare($sql_check_assignment);
    $stmt_check_assignment->bind_param('ii', $assignment_id, $class_id);
    $stmt_check_assignment->execute();
    $result_check_assignment = $stmt_check_assignment->get_result();

    if ($result_check_assignment->num_rows > 0) {
        // Check for students who have requested and their requests are accepted for this class
        $sql_students = "SELECT s.student_id, s.first_name, s.last_name 
                         FROM students s
                         INNER JOIN requests r ON s.student_id = r.student_id
                         WHERE r.class_id = ? AND r.status = 'accepted'";
        $stmt_students = $connection->prepare($sql_students);
        $stmt_students->bind_param('i', $class_id);
        $stmt_students->execute();
        $result_students = $stmt_students->get_result();

        // Prepare SQL statement to insert or update student marks
        $sql_insert = "INSERT INTO student_marks (student_id, class_id, assignment_id, marks) VALUES (?, ?, ?, ?)
                       ON DUPLICATE KEY UPDATE marks = VALUES(marks)";
        $stmt_insert = $connection->prepare($sql_insert);

        while ($row_student = $result_students->fetch_assoc()) {
            $student_id = $row_student['student_id'];
            if (isset($marks[$student_id])) {
                $mark = $marks[$student_id];
                $stmt_insert->bind_param('iiii', $student_id, $class_id, $assignment_id, $mark);
                $stmt_insert->execute();
            }
        }

        $message = "Marks updated successfully.";
    } else {
        $message = "Error: Invalid assignment ID or no students found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Student Marks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #f0f0f0;
        }
        .header {
            width: 100%;
            background: #1bafd4;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header img {
            height: 50px;
            width: auto;
        }
        .header a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .header a:hover {
            background-color: #575757;
        }
        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: left;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f4f4f4;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            color: white;
        }
        .message.success {
            background: #28a745;
        }
        .message.error {
            background: #dc3545;
        }
        footer {
            background-color: #1bafd4;
            color: white;
            text-align: center;
            padding: 10px 20px;
            width: 100%;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const classSelect = document.getElementById('class_id');
            const assignmentSelect = document.getElementById('assignment_id');
            classSelect.addEventListener('change', function() {
                const class_id = this.value;
                fetchAssignments(class_id);
            });

            function fetchAssignments(class_id) {
                fetch('?action=getAssignments&class_id=' + class_id)
                    .then(response => response.json())
                    .then(data => {
                        assignmentSelect.innerHTML = '<option value="">Select Assignment</option>';
                        data.forEach(assignment => {
                            const option = document.createElement('option');
                            option.value = assignment.assignment_id;
                            option.textContent = assignment.title;
                            assignmentSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching assignments:', error));
            }
        });
    </script>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo">
        <div class="button-group">
            <a href="ClassInformation.php">Back to Class Information</a>
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
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
                    <label for="assignment_id">Select Assignment:</label>
                    <select id="assignment_id" name="assignment_id" required>
                        <option value="">Select Assignment</option>
                        <!-- Options will be populated dynamically using JavaScript -->
                    </select>
                </div>
                <div class="field">
                    <label for="marks">Enter Marks:</label>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <h2><?php echo htmlspecialchars($class['class_name']); ?></h2>
                            <div>
                                <?php
                                $sql_students = "SELECT s.student_id, s.first_name, s.last_name 
                                                 FROM students s
                                                 INNER JOIN requests r ON s.student_id = r.student_id
                                                 WHERE r.class_id = ? AND r.status = 'accepted'";
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
                    <?php endif; ?>
                </div>
                <button type="submit">Post Marks</button>
            </form>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
