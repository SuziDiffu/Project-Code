<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as TutorRequest.php

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

// Retrieve requests from the database for the logged-in tutor
$sql_requests = "SELECT request_id, student_id, tutor_id, subject_id, date, status FROM requests WHERE tutor_id = ?";
$stmt_requests = $connection->prepare($sql_requests);
$stmt_requests->bind_param('i', $tutor_id);
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();

$requests = [];
if ($result_requests->num_rows > 0) {
    while ($row_request = $result_requests->fetch_assoc()) {
        $requests[] = $row_request;
    }
}

// Handle form submission to accept or reject requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action == 'accept') {
        // Update the request status to 'accepted'
        $sql_update = "UPDATE requests SET status = 'accepted' WHERE request_id = ?";
        $stmt_update = $connection->prepare($sql_update);
        $stmt_update->bind_param('i', $request_id);
        $stmt_update->execute();
    } elseif ($action == 'reject') {
        // Update the request status to 'rejected'
        $sql_update = "UPDATE requests SET status = 'rejected' WHERE request_id = ?";
        $stmt_update = $connection->prepare($sql_update);
        $stmt_update->bind_param('i', $request_id);
        $stmt_update->execute();
    }

    // Redirect to avoid form resubmission on refresh
    header("Location: TutorRequest.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Requests</title>
    <style>
        /* Styles can be adjusted based on the previous examples */
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .action-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-buttons button.accept {
            background-color: #28a745;
            color: white;
        }
        .action-buttons button.reject {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo">
        <div class="button-group">
            <a href="teacherDashboard.php">Homepage</a>
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <h1>Tutor Requests</h1>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Student ID</th>
                        <th>Tutor ID</th>
                        <th>Subject ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['tutor_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['subject_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['date']); ?></td>
                            <td><?php echo htmlspecialchars($request['status']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['request_id']); ?>">
                                            <button type="submit" name="action" value="accept" class="accept">Accept</button>
                                            <button type="submit" name="action" value="reject" class="reject">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
