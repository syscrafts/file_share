<?php
session_start();

include 'db_connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['file_id'])) {
    $file_id = $_GET['file_id'];

    // Fetch the file details from the database
    $stmt = $conn->prepare("SELECT file_name, file_path FROM files WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $file_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($file_name, $file_path);
    $stmt->fetch();
    $stmt->close();

    // Check if the file exists
    if ($file_path && file_exists($file_path)) {
        // Delete the file from the server
        unlink($file_path);

        // Delete the file entry from the database
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $stmt->close();

        $message = "File '$file_name' has been deleted successfully.";
    } else {
        $message = "File not found or you do not have permission to delete this file.";
    }

    // Redirect back to the dashboard with a message
    header("Location: dashboard.php?message=" . urlencode($message));
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>

