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

    // Retrieve user input from the registration form
    $username = $_POST['username'];
    $email = $_POST['email']; // Retrieve email from the form
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $email_check_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $email_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // If email exists
        // Display an alert message on the registration page
        echo "<script>alert('Email is already registered');</script>";
    } else { // If email is not registered, proceed with registration logic
        // Prepare and bind the SQL statement to insert user data into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Set session variables upon successful registration
            $_SESSION['user_id'] = $conn->insert_id; // Assign the newly generated user ID
            $_SESSION['username'] = $username; // Assign the username
            header("Location: profile.php"); // Redirect to profile.php
            exit();
        } else {
            $_SESSION['registration_error'] = "Error occurred while processing the registration.";
        }

        // Close statement
        $stmt->close();
    }

    // Close database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>
    <h1>User Registration</h1>
    <?php if (isset($_SESSION['registration_error'])): ?>
        <p><?php echo $_SESSION['registration_error']; ?></p>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
