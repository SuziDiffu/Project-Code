<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'tutor') {
    header("Location: \login.php");//this specific part of code takes users who are not teachers back to login page
    exit();
}

// Teacher-specific content
echo "Welcome, Teacher!";
?>