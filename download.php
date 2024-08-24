<?php
include 'db_connection.php';

if (isset($_GET['file_id'])) {
    $file_id = $_GET['file_id'];

    // Fetch the file information from the database
    $stmt = $conn->prepare("SELECT file_name, file_path FROM files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->bind_result($file_name, $file_path);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "Invalid file ID.";
}
?>

