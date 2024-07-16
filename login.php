<?php
session_start();
require 'database/db.php';

use Database\Database; // Use the Database namespace

// Instantiating the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

// 1. Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 2. Prepare and Execute SQL Query for Tutors
    $sql_tutor = "SELECT * FROM tutors WHERE email = ?";
    $stmt_tutor = $connection->prepare($sql_tutor);
    $stmt_tutor->bind_param('s', $email);
    $stmt_tutor->execute();
    $result_tutor = $stmt_tutor->get_result();
    
    // 3. Prepare and Execute SQL Query for Students
    $sql_student = "SELECT * FROM students WHERE email = ?";
    $stmt_student = $connection->prepare($sql_student);
    $stmt_student->bind_param('s', $email);
    $stmt_student->execute();
    $result_student = $stmt_student->get_result();

    // 4. Prepare and Execute SQL Query for Admins
    $sql_admin = "SELECT * FROM admin WHERE email = ?";
    $stmt_admin = $connection->prepare($sql_admin);
    $stmt_admin->bind_param('s', $email);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_tutor->num_rows > 0) {
        $user = $result_tutor->fetch_assoc();
        if (password_verify($password, $user['password'])) /*password_verify hashes password*/{
            $_SESSION['user_id'] = $user['tutor_id'];
            $_SESSION['user_role'] = 'tutor';
            header("Location: user/teacherDashboard.php");
            exit;
        } else {
            echo "Invalid email or password";
        }
    } elseif ($result_student->num_rows > 0) {
        $user = $result_student->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['student_id'];
            $_SESSION['user_role'] = 'student';
            header("Location: user/studentDashboard.php");
            exit;
        } else {
            echo "Invalid email or password";
        }
    } elseif ($result_admin->num_rows > 0) {
        $user = $result_admin->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['admin_id'];
            $_SESSION['user_role'] = 'admin';
            header("Location: user/admin.php");
            exit;
        } else {
            echo "Invalid email or password";
        }
    } else {
        echo "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
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
            justify-content: center;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.8); /* Added background color for better text visibility */
        }
        h1 {
            margin: 10px 0;
            color: #333;
            text-align: center;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .button {
            padding: 15px 25px;
            font-size: 16px;
            color: white;
            background: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            box-sizing: border-box;
        }
        .button:hover {
            background-color: #0056b3;
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
        <a href="index.php">Home</a>
    </div>
    <div class="main">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" class="button">Login</button>
        </form>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy;
           <?php echo date('d,m,Y'); ?>
        </p>
    </footer>   
</body>
</html>
