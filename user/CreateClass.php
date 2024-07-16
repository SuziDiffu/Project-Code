<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as createClass.php

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a tutor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    header("Location: login.php");
    exit;
}
// Instantiating the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

$form_submitted = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = $_POST['class_name'];
    $tutor_id = $_POST['tutor_id'];
    $teacher_name = $_POST['teacher_name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    // Ensuring that the column names and count match the table schema
    $sql = "INSERT INTO class (class_name, tutor_id, teacher_name, email, subject, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('sissss', $class_name, $tutor_id, $teacher_name, $email, $subject, $description);

    if ($stmt->execute()) {
        $message = "Class created successfully.";
        $form_submitted = true;
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Class</title>
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
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            text-align: left;
            width: 100%;
            max-width: 600px;
            margin: 0 auto; /* Center the form container */
        }
        .form-container .field {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .form-container label {
            color: #333;
            font-weight: bold;
        }
        .form-container input, .form-container textarea {
            width: calc(100% - 150px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-container button {
            background: #007BFF;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background: #0056b3;
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
                <a href="../login.php">Logout</a>
            </div>
            <div class="form-container">
                <h1>Create Class</h1>
                <?php if (isset($message)): ?>
                    <p><?php echo $message; ?></p>
                <?php endif; ?>
                <?php if (!$form_submitted): ?>
                <form method="POST" action="createClass.php">
                    <div class="field">
                        <label for="class_name">Class Name:</label>
                        <input type="text" id="class_name" name="class_name" required>
                    </div>
                    <div class="field">
                        <label for="tutor_id">Tutor ID:</label>
                        <input type="text" id="tutor_id" name="tutor_id" required>
                    </div>
                    <div class="field">
                        <label for="teacher_name">Teacher Name:</label>
                        <input type="text" id="teacher_name" name="teacher_name" required>
                    </div>
                    <div class="field">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="field">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="field">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    <button type="submit">Create Class</button>
                </form>
                <?php endif; ?>
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
