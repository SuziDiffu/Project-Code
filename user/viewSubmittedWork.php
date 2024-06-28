<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as viewSubmittedWork.php

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
$sql = "SELECT class_id, class_name FROM class WHERE tutor_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $tutor_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
} else {
    header("Location: createClass.php");
    exit;
}

// Handle form submission to view submitted work
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];

    $sql = "SELECT s.first_name, s.last_name, a.title, sw.file_path, sw.submitted_date
            FROM submitted_work sw
            JOIN assignments a ON sw.assignment_id = a.assignment_id
            JOIN students s ON sw.student_id = s.student_id
            WHERE a.class_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $submitted_work = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $submitted_work[] = $row;
        }
    } else {
        $message = "No submitted work found for the selected class.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submitted Work</title>
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
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background: #0056b3;
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
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            color: white;
        }
        .message.success {
            background: #28a745;
        }
        .message.error {
            background: #dc3545;
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
        <a href="classInformation.php">Back to Class Information</a>
        <a href="../login.php">Logout</a>
    </div>
    <div class="main">
        <div class="content">
            <h1>View Submitted Work</h1>
            <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'error') !== false ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            <form method="POST">
                <label for="class_id">Class</label>
                <select name="class_id" id="class_id" required>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo htmlspecialchars($class['class_id']); ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="View Submitted Work">
            </form>

            <?php if (isset($submitted_work) && count($submitted_work) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Assignment Title</th>
                        <th>Submitted File</th>
                        <th>Submitted Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submitted_work as $work): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($work['first_name'] . ' ' . $work['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($work['title']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($work['file_path']); ?>" target="_blank">View File</a></td>
                        <td><?php echo htmlspecialchars($work['submitted_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
