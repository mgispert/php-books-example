<?php
// Start the session
session_start();

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protected Page</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>This is a protected page. Only authenticated users can access it.</p>
    <p>Put your protected content here...</p>
    <a href="logout.php">Logout</a> <!-- Add a logout link to allow users to logout -->
</body>
</html>
