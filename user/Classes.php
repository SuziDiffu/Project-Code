<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as classes.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Instantiate the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Retrieve student information from the database
$student_id = $_SESSION['user_id'];

// Retrieve the list of classes the student is enrolled in with status 'accepted'
$sql_classes = "
    SELECT c.class_id, c.class_name, t.tutor_id AS teacher_id, t.first_name AS teacher_name, t.email, t.subject
    FROM requests r
    JOIN tutors t ON r.tutor_id = t.tutor_id
    JOIN class c ON t.tutor_id = c.tutor_id
    WHERE r.student_id = ? AND r.status = 'accepted'
";
$stmt_classes = $connection->prepare($sql_classes);
$stmt_classes->bind_param('i', $student_id);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();

$classes = [];
if ($result_classes->num_rows > 0) {
    while ($row_class = $result_classes->fetch_assoc()) {
        $classes[] = $row_class;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Classes</title>
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
            display: flex;
            width: 100%;
            max-width: 1200px;
            align-items: flex-start;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-right: 20px;
            width: 150px; /* Same width for all buttons */
        }
        .button-group a {
            background: #007BFF;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            width: 100%; /* width of buttons */
            text-align: center; /* Center text */
        }
        .button-group a:hover {
            background: #0056b3;
        }
        .classes-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
        }
        .classes-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .classes-container th, .classes-container td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        .classes-container th {
            background: #007BFF;
            color: white;
        }
        .classes-container td {
            background: #f9f9f9;
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
        <div>
            <a href="studentDashboard.php">Back to Student Dashboard</a>
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <div class="button-group">
                <a href="studentDashboard.php">Back to Student Dashboard</a>
            </div>
            <div class="classes-container">
                <h1>Enrolled Classes</h1>
                <?php if (!empty($classes)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Class ID</th>
                                <th>Class Name</th>
                                <th>Teacher ID</th>
                                <th>Teacher Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($class['class_id']); ?></td>
                                    <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['teacher_id']); ?></td>
                                    <td><?php echo htmlspecialchars($class['teacher_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['email']); ?></td>
                                    <td><?php echo htmlspecialchars($class['subject']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You are not enrolled in any classes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
