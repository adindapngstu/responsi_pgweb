<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "diy_universitas";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if id column exists
$result = $conn->query("SHOW COLUMNS FROM database_univ LIKE 'id'");
if ($result->num_rows == 0) {
    // Add id column if it doesn't exist
    $sql = "ALTER TABLE database_univ ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST";
    if ($conn->query($sql) === TRUE) {
        echo "ID column added successfully";
    } else {
        echo "Error adding ID column: " . $conn->error;
    }
} else {
    echo "ID column already exists";
}

$conn->close();
?>
