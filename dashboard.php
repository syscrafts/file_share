<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

$message = '';
$upload_dir = __DIR__ . '/uploads/';

// Define allowed file types and maximum file size (in bytes)
$allowed_file_types = ['pdf', 'jpg', 'jpeg', 'png', 'txt'];
$max_file_size = 25 * 1024 * 1024; // 25MB in bytes
$virustotal_api_key = '451f69dc093cbb15c68ca3692a34f2e0818ae4a28d81d6f9645fded2a5943b3d'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_name = basename($file['name']);
    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_size = $file['size'];
    $target_file = $upload_dir . $file_name;

    // Check if the file type is allowed
    if (!in_array($file_type, $allowed_file_types)) {
        $message = 'File type not allowed. Please upload a PDF, JPG, PNG, or TXT file.';
    } elseif ($file_size > $max_file_size) {
        $message = 'File size should be less than 25MB.';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'An error occurred during file upload.';
    } else {
        // Scan the file with VirusTotal
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.virustotal.com/vtapi/v2/file/scan',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'apikey' => $virustotal_api_key,
                'file' => new CURLFile($file['tmp_name'])
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $message = 'Error scanning file for viruses: ' . $err;
        } else {
            $response_data = json_decode($response, true);
            $scan_id = $response_data['scan_id'];

            // Wait and get the scan report
            sleep(30); // Adjust the sleep time based on your needs

            // Fetch the scan report
            $report_url = 'https://www.virustotal.com/vtapi/v2/file/report';
            $report_params = http_build_query([
                'apikey' => $virustotal_api_key,
                'resource' => $scan_id,
            ]);

            $report_response = file_get_contents($report_url . '?' . $report_params);
            $report_data = json_decode($report_response, true);

            if ($report_data['positives'] > 0) {
                $message = 'Virus infected file. Upload terminated.';
            } else {
                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    // Store the file information in the database
                    $stmt = $conn->prepare("INSERT INTO files (user_id, file_name, file_path) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $_SESSION['user_id'], $file_name, $target_file);
                    $stmt->execute();
                    $stmt->close();

                    $message = 'File uploaded successfully.';
                } else {
                    $message = 'There was an error moving the uploaded file.';
                }
            }
        }
    }
}

// Fetch the user's uploaded files
$stmt = $conn->prepare("SELECT id, file_name, upload_time FROM files WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$files = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-size: cover;
            background-position: center;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .actions a {
            margin-right: 10px;
            cursor: pointer;
        }

        .logout-container {
            text-align: center;
            margin-top: 20px;
        }

        .logout-container button {
            background-color: #ff4c4c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-container button:hover {
            background-color: #ff1c1c;
        }

        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .message.success {
            background-color: #4caf50;
            color: white;
        }

        .message.error {
            background-color: #f44336;
            color: white;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 300px;
            text-align: center;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="file">Upload a file:</label>
            <input type="file" name="file" id="file" required>
            <button type="submit">Upload</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <h2>Your Uploaded Files</h2>
        <?php if (count($files) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Uploaded On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                            <td><?php echo $file['upload_time']; ?></td>
                            <td class="actions">
                                <a href="download.php?file_id=<?php echo $file['id']; ?>">Download</a>
                                <a onclick="generateQRCode('<?php echo "http://localhost.com/file_share/download.php?file_id=" . $file['id']; ?>')">Shareable Link</a>
                                <a href="delete.php?file_id=<?php echo $file['id']; ?>" onclick="return confirm('Are you sure you want to delete this file?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven't uploaded any files yet.</p>
        <?php endif; ?>

        <div class="logout-container">
            <form method="POST" action="logout.php">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <!-- Modal for QR Code -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="qrcode"></div>
        </div>
    </div>

    <script>
        function generateQRCode(link) {
            var qrModal = document.getElementById("qrModal");
            var qrCodeContainer = document.getElementById("qrcode");

            // Clear any existing QR codes
            qrCodeContainer.innerHTML = "";

            // Generate the QR code
            new QRCode(qrCodeContainer, {
                text: link,
                width: 256,
                height: 256
            });

            // Display the modal
            qrModal.style.display = "block";
        }

        function closeModal() {
            var qrModal = document.getElementById("qrModal");
            qrModal.style.display = "none";
        }

        // Close the modal when the user clicks anywhere outside of it
        window.onclick = function(event) {
            var qrModal = document.getElementById("qrModal");
            if (event.target == qrModal) {
                qrModal.style.display = "none";
            }
        }
    </script>
</body>
</html>

