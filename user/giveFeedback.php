<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as giveFeedback.php

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

// Retrieve sessions for the logged-in student
$sql_sessions = "SELECT session_id, tutor_id FROM sessions WHERE student_id = ?";
$stmt_sessions = $connection->prepare($sql_sessions);
$stmt_sessions->bind_param('i', $student_id);
$stmt_sessions->execute();
$result_sessions = $stmt_sessions->get_result();

$sessions = [];
if ($result_sessions->num_rows > 0) {
    while ($row_session = $result_sessions->fetch_assoc()) {
        $sessions[] = $row_session;
    }
}
$stmt_sessions->close();

// Handle form submission
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'];
    $tutor_id = $_POST['tutor_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $sql_insert = "INSERT INTO feedback (session_id, student_id, tutor_id, rating, comments) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $connection->prepare($sql_insert);
    $stmt_insert->bind_param('iiiss', $session_id, $student_id, $tutor_id, $rating, $comments);
    $stmt_insert->execute();
    $stmt_insert->close();

    // Redirect to the same page with a success query parameter
    header("Location: giveFeedback.php?success=1");
    exit;
}

// Check for success query parameter
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = 'Feedback submitted successfully.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback</title>
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
        input, select, textarea {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        textarea {
            resize: vertical;
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
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 15px;
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
            <a href="login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <h1>Give Feedback</h1>
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="giveFeedback.php">
                <label for="session_id">Session ID:</label>
                <select id="session_id" name="session_id" required>
                    <?php foreach ($sessions as $session): ?>
                        <option value="<?php echo htmlspecialchars($session['session_id']); ?>">
                            <?php echo htmlspecialchars($session['session_id']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="tutor_id">Tutor ID:</label>
                <select id="tutor_id" name="tutor_id" required>
                    <?php foreach ($sessions as $session): ?>
                        <option value="<?php echo htmlspecialchars($session['tutor_id']); ?>">
                            <?php echo htmlspecialchars($session['tutor_id']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="rating">Rating(/5):</label>
                <input type="number" id="rating" name="rating" min="1" max="5" required>

                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" rows="4" required></textarea>

                <button type="submit">Submit Feedback</button>
            </form>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
