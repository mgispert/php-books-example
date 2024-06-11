<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure all required fields are filled
    if (isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if new password and confirm password match
        if ($new_password === $confirm_password) {
            // Database connection
            $servername = "localhost";
            $username = "root"; // Your database username
            $password = ""; // Your database password
            $dbname = "simple_books_db"; // Your database name

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the user's password in the database
            $sql = "UPDATE users SET password = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['password_reset_success'] = true;
                header("Location: login.php"); // Redirect to login page after successful password reset
                exit();
            } else {
                $_SESSION['password_reset_error'] = "Error occurred while resetting the password.";
            }

            // Close statement and database connection
            $stmt->close();
            $conn->close();
        } else {
            $_SESSION['password_reset_error'] = "Passwords do not match.";
        }
    } else {
        $_SESSION['password_reset_error'] = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset</h1>
    <?php if (isset($_SESSION['password_reset_error'])): ?>
        <p><?php echo $_SESSION['password_reset_error']; ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>
        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
