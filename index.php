<?php

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

// Pagination settings
$booksPerPage = 3;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $booksPerPage;

// Initialize search variable
$search = '';

// Check if search query is set
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// SQL query for search
$sql = "SELECT title, author, description, cover_image_url, purchase_link FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR description LIKE '%$search%'";

// Count total number of search results
$totalResultsSql = "SELECT COUNT(*) AS total FROM ($sql) AS searchResults";
$totalResultsResult = $conn->query($totalResultsSql);
$totalResultsRow = $totalResultsResult->fetch_assoc();
$totalResults = $totalResultsRow['total'];

// Calculate total pages
$totalPages = ceil($totalResults / $booksPerPage);

// SQL query with pagination
$sql .= " LIMIT $booksPerPage OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assuming you have a CSS file for styling -->
</head>
<body>
    <h1>Books</h1>
    <form action="index.php" method="GET">
        <input type="text" name="search" placeholder="Search...">
        <button type="submit">Search</button>
    </form>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="book">
            <h2><?php echo $row['title']; ?></h2>
            <p>Author: <?php echo $row['author']; ?></p>
            <p><?php echo $row['description']; ?></p>
            <img src="<?php echo $row['cover_image_url']; ?>" alt="Book Cover">
            <p><a href="<?php echo $row['purchase_link']; ?>">Purchase</a></p>
        </div>
    <?php endwhile; ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?search=<?php echo $search; ?>&page=<?php echo $page - 1; ?>">Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?php echo $search; ?>&page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?search=<?php echo $search; ?>&page=<?php echo $page + 1; ?>">Next</a>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
