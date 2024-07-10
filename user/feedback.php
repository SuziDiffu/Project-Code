<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as feedback.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a tutor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    header("Location: login.php");
    exit;
}

// Instantiate the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Get the tutor ID from the session
$tutor_id = $_SESSION['user_id'];

// Retrieve feedback for the logged-in tutor
$sql_feedback = "SELECT feedback_id, session_id, student_id, tutor_id, rating, comments FROM feedback WHERE tutor_id = ?";
$stmt_feedback = $connection->prepare($sql_feedback);
$stmt_feedback->bind_param('i', $tutor_id);
$stmt_feedback->execute();
$result_feedback = $stmt_feedback->get_result();

$feedbacks = [];
if ($result_feedback->num_rows > 0) {
    while ($row_feedback = $result_feedback->fetch_assoc()) {
        $feedbacks[] = $row_feedback;
    }
}
$stmt_feedback->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
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
            background-color: #f0f0f0;
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
            <a href="teacherDashboard.php">Back to Tutor Dashboard</a>
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <h1>Feedback</h1>
            <?php if (count($feedbacks) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Feedback ID</th>
                            <th>Session ID</th>
                            <th>Student ID</th>
                            <th>Tutor ID</th>
                            <th>Rating</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['feedback_id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['session_id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['tutor_id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['comments']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No feedback found.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
