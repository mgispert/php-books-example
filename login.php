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

    // Retrieve username and password from the login form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve hashed password from the database based on the entered username
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify the entered password against the hashed password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set up session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Redirect to the dashboard or another authenticated page
            header("Location: profile.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($error_message)): ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
