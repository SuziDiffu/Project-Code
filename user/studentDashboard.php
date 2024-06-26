<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as studentDashboard.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Instantiating the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Retrieve student information from the database
$student_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        h1 {
            margin: 10px 0;
            color: #333;
            text-align: center;
        }
        .search-bar {
            width: 100%;
            max-width: 600px;
            display: flex;
            margin-bottom: 20px;
        }
        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
        }
        .search-bar button {
            padding: 10px 20px;
            border: none;
            background: #007BFF;
            color: white;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background: #0056b3;
        }
        .profile {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
            text-align: left;
        }
        .profile .field {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .profile label {
            color: #333;
            font-weight: bold;
        }
        .profile p {
            margin: 0;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 70%;
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
        <a href="logout.php">Logout</a>
    </div>
    <div class="main">
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <button>Search</button>
        </div>
        <h1>Student Dashboard</h1>
        <div class="profile">
            <div class="field">
                <label>Student ID:</label>
                <p><?php echo htmlspecialchars($student['student_id']); ?></p>
            </div>
            <div class="field">
                <label>First Name:</label>
                <p><?php echo htmlspecialchars($student['first_name']); ?></p>
            </div>
            <div class="field">
                <label>Last Name:</label>
                <p><?php echo htmlspecialchars($student['last_name']); ?></p>
            </div>
            <div class="field">
                <label>Grade:</label>
                <p><?php echo htmlspecialchars($student['grade']); ?></p>
            </div>
            <div class="field">
                <label>Email:</label>
                <p><?php echo htmlspecialchars($student['email']); ?></p>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy;
           <?php echo date('Y'); ?>
        </p>
    </footer>
</body>
</html>
