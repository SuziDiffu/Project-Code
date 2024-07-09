<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as classInformation.php

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
$sql = "SELECT class_id, class_name, teacher_name, email FROM class WHERE tutor_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $tutor_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
} else {
    header("Location: createClass.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Information</title>
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
            width: 100%; /* Same width for all buttons */
            text-align: center; /* Center text */
        }
        .button-group a:hover {
            background: #0056b3;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            text-align: left;
            width: 100%;
            max-width: 800px;
            margin: 0 auto; /* Center the table container */
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
    </div>
    <div class="main">
        <div class="content">
            <div class="button-group">
                <a href="teacherDashboard.php">Homepage</a>
                <a href="postAssignment.php">Post Assignment</a>
                <a href="session.php">Submit Session Details</a>
                <a href="paymentDetails.php">Payment Details</a>
                <a href="viewSubmittedWork.php">View Submitted Work</a>
                <a href="viewStudentRecords.php">View Student Records</a>
                <a href="postMarks.php">Post Marks</a>
                <a href="paymentDetails.php">Payment Details</a>
                <a href="feedback.php">View Feedback</a>
                <a href="../login.php">Logout</a>
            </div>
            <div class="table-container">
                <h1>Class Information</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Class ID</th>
                            <th>Class Name</th>
                            <th>Teacher Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['class_id']); ?></td>
                            <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($class['teacher_name']); ?></td>
                            <td><?php echo htmlspecialchars($class['email']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
