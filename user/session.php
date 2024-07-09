<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as session.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    header("Location: login.php");
    exit;
}

// Instantiate the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $tutor_id = $_POST['tutor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $_POST['status'];

    $sql_insert = "INSERT INTO sessions (student_id, tutor_id, date, time, status) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $connection->prepare($sql_insert);
    $stmt_insert->bind_param('iisss', $student_id, $tutor_id, $date, $time, $status);
    $stmt_insert->execute();
    $stmt_insert->close();

    // Redirect to the same page after submission to avoid form resubmission
    header("Location: session.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Session Details</title>
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
        input, select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #1bafd4;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #168aad;
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
            <a href="classInformation.php">Back to Class Information</a>
            <a href="login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <h1>Submit Session Details</h1>
            <form method="POST" action="session.php">
                <label for="student_id">Student ID:</label>
                <input type="number" id="student_id" name="student_id" required>

                <label for="tutor_id">Tutor ID:</label>
                <input type="number" id="tutor_id" name="tutor_id" required>

                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>

                <label for="time">Time:</label>
                <input type="time" id="time" name="time" required>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="attended">Attended</option>
                    <option value="absent">Absent</option>
                </select>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
