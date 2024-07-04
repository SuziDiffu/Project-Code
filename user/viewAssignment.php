<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as viewAssignments.php

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

// Retrieve assignments for the accepted classes of the logged-in student
$sql_assignments = "
    SELECT a.assignment_id, a.class_id, a.title, a.description, a.due_date
    FROM assignments a
    JOIN requests r ON a.class_id = r.class_id
    WHERE r.student_id = ? AND r.status = 'accepted'";
$stmt_assignments = $connection->prepare($sql_assignments);
$stmt_assignments->bind_param('i', $student_id);
$stmt_assignments->execute();
$result_assignments = $stmt_assignments->get_result();

$assignments = [];
if ($result_assignments->num_rows > 0) {
    while ($row_assignment = $result_assignments->fetch_assoc()) {
        $assignments[] = $row_assignment;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignments</title>
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
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <h1>View Assignments</h1>
            <?php if (!empty($assignments)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Assignment ID</th>
                            <th>Class ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['assignment_id']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['class_id']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No assignments found.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
