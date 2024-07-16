<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
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
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Added background color for better text visibility */
        }
        h1, h4 {
            margin: 10px 0;
            color: #333;
            text-align: center;
        }
        .button {
            margin: 10px;
            padding: 15px 25px;
            font-size: 16px;
            color: white;
            background: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            transition: background-color 0.3s;
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
            position: absolute;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php echo '<img src="logo.png" alt="Logo">'; ?>
        <a href="index.php">Home</a>
    </div>
    <div class="main">
        <h1><i>Please Select One</i></h1>
        <h4><i>Students to select student registration while teachers to select teacher registration</i></h4>
        <div>
            <a href="studentRegistration.php" class="button">Student Registration</a>
            <a href="teacherRegistration.php" class="button">Teacher Registration</a>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy;
           <?php echo date('d, m, Y'); ?>
        </p>
    </footer>   
</body>
</html>
