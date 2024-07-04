<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as studentDashboard.php

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
$sql_student = "SELECT * FROM students WHERE student_id = ?";
$stmt_student = $connection->prepare($sql_student);
$stmt_student->bind_param('i', $student_id);
$stmt_student->execute();
$result_student = $stmt_student->get_result();
$student = $result_student->fetch_assoc();

// Handle search form submission
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_term = '%' . $_POST['search'] . '%'; // Adding wildcard for partial matching

    // Query to find tutors teaching the subject matching the search term and join with class table
    $sql_tutors = "
        SELECT t.*, c.class_id, c.class_name 
        FROM tutors t
        LEFT JOIN class c ON t.tutor_id = c.tutor_id
        WHERE t.subject LIKE ?
    ";
    $stmt_tutors = $connection->prepare($sql_tutors);
    $stmt_tutors->bind_param('s', $search_term);
    $stmt_tutors->execute();
    $result_tutors = $stmt_tutors->get_result();

    if ($result_tutors->num_rows > 0) {
        while ($row_tutor = $result_tutors->fetch_assoc()) {
            $search_results[] = $row_tutor;
        }
    }
}
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
            width: 70%; /* width of buttons*/
            text-align: center; /* Center text */
        }
        .button-group a:hover {
            background: #0056b3;
        }
        .profile-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 600px;
        }
        .search-bar {
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
        .search-results {
            margin-top: 20px;
            max-width: 600px;
        }
        .search-results h2 {
            margin-bottom: 10px;
        }
        .search-results ul {
            list-style-type: none;
            padding: 0;
        }
        .search-results li {
            margin-bottom: 10px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo">
        <div>
            <a href="studentDashboard.php">Homepage</a>
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <div class="button-group">
                <a href="studentDashboard.php">Homepage</a>
                <a href="classes.php">Classes</a>
                <a href="RequestTutor.php">Request Tutor</a>
                <a href="viewAssignment.php">View Assignment</a>
                <a href="viewMarks.php">View Marks</a>
                <a href="../login.php">Logout</a>
            </div>
            <div class="profile-container">
                <div class="search-bar">
                    <form method="POST" action="">
                        <input type="text" name="search" placeholder="Search...">
                        <button type="submit">Search</button>
                    </form>
                </div>
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
                <?php if (!empty($search_results)): ?>
                    <div class="search-results">
                        <h2>Search Results</h2>
                        <ul>
                            <?php foreach ($search_results as $result): ?>
                                <li>
                                    <strong>Tutor ID:</strong> <?php echo htmlspecialchars($result['tutor_id']); ?><br>
                                    <strong>First Name:</strong> <?php echo htmlspecialchars($result['first_name']); ?><br>
                                    <strong>Last Name:</strong> <?php echo htmlspecialchars($result['last_name']); ?><br>
                                    <strong>Subject:</strong> <?php echo htmlspecialchars($result['subject']); ?><br>
                                    <strong>Email:</strong> <?php echo htmlspecialchars($result['email']); ?><br>
                                    <strong>Class ID:</strong> <?php echo htmlspecialchars($result['class_id']); ?><br>
                                    <strong>Class Name:</strong> <?php echo htmlspecialchars($result['class_name']); ?><br>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
