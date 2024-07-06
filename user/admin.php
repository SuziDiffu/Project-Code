<?php
session_start();
require '../database/db.php';

use Database\Database; // Use the Database namespace

// Ensure the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Instantiating the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// Get admin information
$admin_id = $_SESSION['user_id'];
$sql = "SELECT admin_id, name, email FROM admin WHERE admin_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    echo "No admin found with the given ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
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
            align-items: center;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        .profile-container {
            display: flex;
            align-items: flex-start;
        }
        .buttons {
            margin-right: 20px;
            display: flex;
            flex-direction: column;
        }
        .button {
            padding: 10px 20px;
            margin-bottom: 10px;
            font-size: 16px;
            color: white;
            background: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .profile {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        .profile p {
            margin: 10px 0;
            color: #333;
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
        <?php echo '<img src="logo.png" alt="Logo">'; ?>
        <a href="../login.php">Logout</a>
    </div>
    <div class="main">
        <div class="profile-container">
            <div class="buttons">
                <a href="dropStudent.php" class="button">Drop Student</a>
                <a href="dropTeacher.php" class="button">Drop Teacher</a>
            </div>
            <div class="profile">
                <p><strong>Admin ID:</strong> <?php echo htmlspecialchars($admin['admin_id']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
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
