<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
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
            justify-content: center;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
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
        input, select {
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
        <h1>Student Registration</h1>
        
        <?php
        require_once 'database/db.php';
        use Database\Database; // Use the Database namespace

        // Instantiating the Database class
        $db = new Database();
        $connection = $db->connection; // Access the database connection
        session_start();
    

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $grade = $_POST['grade'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password !== $confirm_password) {
                echo "<p style='color:red;'>Passwords do not match.</p>";
            } else {
                // Hash the password before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO students (first_name, last_name, email, grade, password) VALUES (?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param('sssss', $first_name, $last_name, $email, $grade, $hashed_password);

                if ($stmt->execute()) {
                    echo "<p style='color:green;'>Registration successful. You can now log in.</p>";
                    header("Location: login.php");
                } else {
                    echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
                }
            }
        }
        ?>

        <form action="studentRegistration.php" method="post">
            <label for="first_name">First Name*</label>
            <input type="text" id="first_name" name="first_name" required>
            
            <label for="last_name">Last Name*</label>
            <input type="text" id="last_name" name="last_name" required>
            
            <label for="email">Email*</label>
            <input type="email" id="email" name="email" required>
            
            <label for="grade">Grade*</label>
            <input type="text" id="grade" name="grade" required>
            
            <label for="password">Password*</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password*</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit" class="button">Register</button>
        </form>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy;
           <?php echo date('d, m, Y'); ?>
        </p>
    </footer>   
</body>
</html>
