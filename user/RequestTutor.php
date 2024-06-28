<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as Request.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Instantiate the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form values
    $student_id = $_POST['student_id'];
    $tutor_id = $_POST['tutor_id'];
    $subject = $_POST['subject'];
    $date = $_POST['date'];

    // Insert the new request into the requests table
    $sql_insert = "INSERT INTO requests (student_id, tutor_id, subject, date) VALUES (?, ?, ?, ?)";
    $stmt_insert = $connection->prepare($sql_insert);
    $stmt_insert->bind_param('iiss', $student_id, $tutor_id, $subject, $date);

    if ($stmt_insert->execute()) {
        $message = 'Request submitted successfully!';
    } else {
        $message = 'Error: ' . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Request</title>
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
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container .field {
            margin-bottom: 15px;
        }
        .form-container label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        .form-container button {
            padding: 10px 20px;
            border: none;
            background: #007BFF;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background: #0056b3;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
                <a href="studentDashboard.php">Back to Student Dashboard</a>
                <a href="classes.php">Classes</a>
                <a href="../login.php">Logout</a><!--this takes the user back to the login page of the website--->
            </div>
            <div class="form-container">
                <h1>Submit Request</h1>
                <?php if (!empty($message)): ?>
                    <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="field">
                        <label for="student_id">Student ID:</label>
                        <input type="text" id="student_id" name="student_id" required>
                    </div>
                    <div class="field">
                        <label for="tutor_id">Tutor ID:</label>
                        <input type="text" id="tutor_id" name="tutor_id" required>
                    </div>
                    <div class="field">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="field">
                        <label for="date">Date:</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <button type="submit">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
