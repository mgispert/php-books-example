<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root"; // Default XAMPP username
    $password = ""; // Default XAMPP password is empty
    $dbname = "simple_books_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO books (title, author, description, cover_image_url, purchase_link) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $author, $description, $cover_image_url, $purchase_link);

    // Set parameters and execute
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $cover_image_url = $_POST['cover_image_url'];
    $purchase_link = $_POST['purchase_link'];
    $stmt->execute();

    echo "New book added successfully";

    $stmt->close();
    $conn->close();

    // Redirect to the main page
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Book</title>
</head>
<body>
    <h1>Add New Book</h1>
    <form action="add_book.php" method="POST">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>
        <label for="author">Author:</label><br>
        <input type="text" id="author" name="author" required><br><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required></textarea><br><br>
        <label for="cover_image_url">Cover Image URL:</label><br>
        <input type="text" id="cover_image_url" name="cover_image_url"><br><br>
        <label for="purchase_link">Purchase Link:</label><br>
        <input type="text" id="purchase_link" name="purchase_link"><br><br>
        <input type="submit" value="Add Book">
    </form>
</body>
</html>
