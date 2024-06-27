<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as studentDashboard.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a teacher(tutor)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    header("Location: login.php");
    exit;
}

// Instantiating the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Retrieve teacher information from the database
$tutor_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tutors WHERE tutor_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
$tutor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Dashboard</title>
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
            justify-content: center;
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
            width: 70%; /* Full width */
            text-align: center; /* Center text */
        }
        .button-group a:hover {
            background: #0056b3;
        }
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center content */
            width: 100%;
            max-width: 600px;
        }
        
        .profile {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            text-align: left;
            width: 100%;
            max-width: 400px; /* Reduced size */
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
    </div>
    <div class="main">
        <div class="content">
            <div class="button-group">
                <a href="teacherDashboard.php">Homepage</a>
                <a href="CreateClass.php">Create Class</a>
                <a href="ClassInformation.php">View Class Information</a>
                <a href="TutorRequests.php"> Tutor Requests</a>
                <a href="../login.php">Logout</a><!--this takes the user back to the login page of the website--->
            </div>
                <div class="profile">
                    <div class="field">
                        <label>Tutor ID:</label>
                        <p><?php echo htmlspecialchars($tutor['tutor_id']); ?></p>
                    </div>
                    <div class="field">
                        <label>First Name:</label>
                        <p><?php echo htmlspecialchars($tutor['first_name']); ?></p>
                    </div>
                    <div class="field">
                        <label>Last Name:</label>
                        <p><?php echo htmlspecialchars($tutor['last_name']); ?></p>
                    </div>
                    <div class="field">
                        <label>Subject:</label>
                        <p><?php echo htmlspecialchars($tutor['subject']); ?></p>
                    </div>
                    <div class="field">
                        <label>Email:</label>
                        <p><?php echo htmlspecialchars($tutor['email']); ?></p>
                    </div>
                    <div class="field">
                        <label>Phone Number:</label>
                        <p><?php echo htmlspecialchars($tutor['phone_no']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy;
           <?php echo date('d,m,Y'); ?>
        </p>
    </footer>
</body>
</html>
