<?php
session_start();
require '../database/db.php';

use Database\Database; // Use the Database namespace

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Instantiating the Database class
$db = new Database();
$connection = $db->connection; // Access the database connection

$message = '';

// Handle form submission to upload work
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $class_id = $_POST['class_id'];
    $file = $_FILES['file'];

    // Validate file
    $allowed_extensions = ['pdf'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (!in_array($file_extension, $allowed_extensions)) {
        $message = "Only PDF files are allowed.";
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "An error occurred during file upload.";
    } else {
        $upload_dir = '../uploads/';
        $file_name = uniqid() . '_' . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Insert file information into the database
            $sql = "INSERT INTO submissions (student_id, class_id, file_path) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param('iis', $student_id, $class_id, $file_path);

            if ($stmt->execute()) {
                $message = "File uploaded successfully.";
            } else {
                $message = "Failed to upload file. Please try again.";
            }
        } else {
            $message = "Failed to move uploaded file.";
        }
    }
}

// Retrieve classes the student is enrolled in
$sql_classes = "
    SELECT c.class_id, c.class_name
    FROM class c
    JOIN requests r ON c.class_id = r.class_id
    WHERE r.student_id = ? AND r.status = 'accepted'";
$stmt_classes = $connection->prepare($sql_classes);
$stmt_classes->bind_param('i', $_SESSION['user_id']);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();
$classes = [];
while ($row = $result_classes->fetch_assoc()) {
    $classes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Work</title>
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
            max-width: 600px;
            text-align: left;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="file"], select {
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
        <a href="studentDashboard.php">Back to Student Dashboard</a>
        <a href="../login.php">Logout</a>
    </div>
    <div class="main">
        <div class="content">
            <h1>Submit Assignment</h1>
            <?php if (!empty($message)): ?>
                <p class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="class_id">Class</label>
                <select id="class_id" name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['class_id']; ?>">
                            <?php echo $class['class_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="file">Upload File</label>
                <input type="file" id="file" name="file" accept=".pdf" required>

                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
    <footer>
        <p>TutorPal, Copyright &copy; <?php echo date('d,m,Y'); ?></p>
    </footer>
</body>
</html>
