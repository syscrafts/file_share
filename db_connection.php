<?php
$host = 'localhost';
$db = 'user_system';
$user = 'samrat';
$pass = 'qwerty21$';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

