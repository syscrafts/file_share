<?php
// Start output buffering to prevent issues with header redirection
ob_start();

// Include the database connection
include 'db_connection.php';

session_start();

$message = ''; // Variable to hold the error or success message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_type = $_POST['login_type'];

    if ($login_type === 'traditional') {
        // Handle traditional login
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($username) && !empty($password)) {
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($user_id, $hashedPassword);
            $stmt->fetch();
            $stmt->close(); // Ensure the statement is closed

            if ($hashedPassword && password_verify($password, $hashedPassword)) {
                // Successful login
                $_SESSION['session_id'] = session_id();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username; // Store username in session

                // Store the session in the database
                $stmt = $conn->prepare("INSERT INTO sessions (session_id, user_id) VALUES (?, ?)");
                $stmt->bind_param("si", $_SESSION['session_id'], $user_id);
                $stmt->execute();
                $stmt->close();

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit(); // Important to stop further script execution
            } else {
                $message = 'Invalid username or password.';
            }
        } else {
            $message = 'Please enter a username and password.';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($message)): ?>
            <div class="message error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="Username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Password" required>

            <button type="submit" name="login_type" value="traditional">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>

