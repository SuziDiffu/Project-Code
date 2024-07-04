<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as viewMarks.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Instantiate the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Get the student ID from the session
$student_id = $_SESSION['user_id'];

// Retrieve marks for the logged-in student
$sql_marks = "
    SELECT sm.student_id, sm.class_id, sm.assignment_id, sm.marks, a.title AS assignment_title, c.class_name
    FROM student_marks sm
    JOIN assignments a ON sm.assignment_id = a.assignment_id
    JOIN class c ON sm.class_id = c.class_id
    WHERE sm.student_id = ?";
$stmt_marks = $connection->prepare($sql_marks);
$stmt_marks->bind_param('i', $student_id);
$stmt_marks->execute();
$result_marks = $stmt_marks->get_result();

$marks = [];
if ($result_marks->num_rows > 0) {
    while ($row_mark = $result_marks->fetch_assoc()) {
        $marks[] = $row_mark;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
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
        footer {
            background-color: #1bafd4;
            color: white;
            text-align: center;
            padding: 10px 20px;
            width: 100%;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo">
        <div class="button-group">
            <a href="studentDashboard.php">Back to Student Dashboard</a>
            <a href="classes.php">Classes</a>
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <h1>View Marks</h1>
            <?php if (!empty($marks)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Class ID</th>
                            <th>Class Name</th>
                            <th>Assignment ID</th>
                            <th>Assignment Title</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($marks as $mark): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mark['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($mark['class_id']); ?></td>
                                <td><?php echo htmlspecialchars($mark['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($mark['assignment_id']); ?></td>
                                <td><?php echo htmlspecialchars($mark['assignment_title']); ?></td>
                                <td><?php echo htmlspecialchars($mark['marks']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No marks found.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
