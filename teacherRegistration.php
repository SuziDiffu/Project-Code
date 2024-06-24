<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
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
        <h1>Teacher Registration</h1>
        <form action="teacher_register.php" method="post">
            <label for="first_name">First Name*</label>
            <input type="text" id="first_name" name="first_name" required>
            
            <label for="last_name">Last Name*</label>
            <input type="text" id="last_name" name="last_name" required>
            
            <label for="email">Email*</label>
            <input type="email" id="email" name="email" required>
            
            <label for="subject">Subject You Teach*</label>
            <input type="text" id="subject" name="subject" required>
            <label for="password">Password*</label>
            <input type="password" id="password" name="password" required><!--type=password dispalys the values that have been input as dots instead of text--->
            
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
