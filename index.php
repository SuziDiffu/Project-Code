
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <style> 
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            width: 100%;
            background-color: #1bafd4;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar .logo {
            height: 50px;
            width: auto;
        }
        .navbar .nav-items {
            display: flex;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }
        .navbar a:hover {
            background-color: #575757;
        }
        .navbar img {
            height: 20px;
            width: 20px;
            margin-right: 8px;
        }
        .main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .about {
            background-color: white;
            padding: 40px;
            width: 80%;
            max-width: 800px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .about h1 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #333;
        }
        .about p {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
        }
        .about ul {
            list-style-type: none;
            padding: 0;
            margin-bottom: 20px;
        }
        .about ul li {
            margin-bottom: 10px;
            color: #555;
        }
        .registration-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .button {
            padding: 15px 25px;
            font-size: 16px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
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
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="logo.png" alt="Logo" class="logo">
        <div class="nav-items">
            <a href="index.php">Home</a>
            <a href="registration.php">Register</a>
            <a href="login.php">Login</a>
            <a href="mailto:infotutorpal@gmail.com">
                <img src="email_icon.png" alt="Email"> infotutorpal@gmail.com
            </a>
        </div>
    </div>
    <div class="main">
        <div class="about">
            <h1><i>An Innovative Approach to Education</i></h1>
            <h2>About Us</h2>
            <p><b>Welcome to TutorPal: Where Students and Teachers Connect Seamlessly!</b></p>
            <p>At TutorPal, we bridge the gap between eager learners and passionate educators, creating a dynamic platform where education meets opportunity. Whether you're a student seeking to master new skills or a teacher looking to share your expertise, TutorPal is your go-to destination for impactful learning experiences.</p>
            <p>For Students:</p>
            <ul>
                <li>Tailored Learning: Find tutors who match your learning style and goals, ensuring you get the most out of every session.</li>
                <li>Diverse Subjects: Access a wide range of subjects and specializations, from math and science to languages.</li>
            </ul>
            <p>For Teachers:</p>
            <ul>
                <li>Reach More Students: Expand your reach and impact by connecting with students from all over the world.</li>
                <li>Grow Your Income: Increase your earnings by offering your expertise to a broad audience.</li>
            </ul>
            <p>For registration, please click register:</p>
            <div class="registration-buttons">
                <a href="studentRegistration.php" class="button">Student Registration</a>
                <a href="teacherRegistration.php" class="button">Teacher Registration</a>
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
