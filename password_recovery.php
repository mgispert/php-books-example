<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Retrieve email from the password recovery form
    $email = $_POST['email'];

    // Check if the email exists in the database
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32)); // Generate a random 64-character token

        // Calculate token expiration (e.g., 1 hour from now)
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store the token and its expiration date in the database
        $sql = "INSERT INTO password_reset_tokens (email, token, expiration) VALUES ('$email', '$token', '$expiration')";
        if ($conn->query($sql) === TRUE) {
            // Send password reset email to the user
            $reset_link = "http://localhost/php-books-example/password_reset.php?token=$token"; // Update with your password reset page URL
            $email_subject = "Password Reset Request";
            $email_body = "To reset your password, please click the following link:\n$reset_link";
            // Send email using your preferred email sending method (e.g., PHP's mail() function, third-party library)
            // Example using PHP's mail() function (ensure your server is properly configured to send emails):
            mail($email, $email_subject, $email_body);
            $_SESSION['password_reset_success'] = true;
        } else {
            $_SESSION['password_reset_error'] = "Error occurred while processing the request.";
        }
    } else {
        $_SESSION['password_reset_error'] = "Email address not found.";
    }

    // Close connection
    $conn->close();

    // Redirect back to the password recovery form
    header("Location: password_recovery.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
</head>
<body>
    <h1>Password Recovery</h1>
    <?php if (isset($_SESSION['password_reset_success'])): ?>
        <p>A password reset link has been sent to your email address.</p>
    <?php elseif (isset($_SESSION['password_reset_error'])): ?>
        <p><?php echo $_SESSION['password_reset_error']; ?></p>
    <?php endif; ?>
    <form action="password_recovery.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
