<?php
session_start();
require '../database/db.php'; // db.php is in a directory called database at the same level as paymentDetails.php

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

// Retrieve payments for the logged-in tutor
$sql_payments = "SELECT payment_id, session_id, tutor_id, amount, date, status FROM payments WHERE tutor_id = ?";
$stmt_payments = $connection->prepare($sql_payments);
$stmt_payments->bind_param('i', $tutor_id);
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();

$payments = [];
if ($result_payments->num_rows > 0) {
    while ($row_payment = $result_payments->fetch_assoc()) {
        $payments[] = $row_payment;
    }
}
$stmt_payments->close();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id']) && isset($_POST['status'])) {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];

    $sql_update = "UPDATE payments SET status = ? WHERE payment_id = ?";
    $stmt_update = $connection->prepare($sql_update);
    $stmt_update->bind_param('si', $status, $payment_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirect to avoid form resubmission
    header("Location: paymentDetails.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
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
            background-color: #f0f0f0;
        }
        .status-buttons form {
            display: inline;
        }
        .status-buttons button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .status-buttons .received {
            background-color: #28a745;
            color: white;
        }
        .status-buttons .not-received {
            background-color: #dc3545;
            color: white;
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
            <a href="tutorDashboard.php">Back to Tutor Dashboard</a>
            <a href="../login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <h1>Payment Details</h1>
            <?php if (count($payments) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Session ID</th>
                            <th>Tutor ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                                <td><?php echo htmlspecialchars($payment['session_id']); ?></td>
                                <td><?php echo htmlspecialchars($payment['tutor_id']); ?></td>
                                <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                <td><?php echo htmlspecialchars($payment['date']); ?></td>
                                <td class="status-buttons">
                                    <form method="POST" action="paymentDetails.php">
                                        <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['payment_id']); ?>">
                                        <button type="submit" name="status" value="received" class="received" <?php if ($payment['status'] === 'received') echo 'disabled'; ?>>Received</button>
                                    </form>
                                    <form method="POST" action="paymentDetails.php">
                                        <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['payment_id']); ?>">
                                        <button type="submit" name="status" value="not_received" class="not-received" <?php if ($payment['status'] === 'not_received') echo 'disabled'; ?>>Not Received</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No payments found.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
