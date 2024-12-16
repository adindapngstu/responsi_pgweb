<?php
$conn = new mysqli('localhost', 'root', '', 'diy_universitas');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add URL column to database_univ table
$sql = "ALTER TABLE database_univ ADD COLUMN website_url VARCHAR(255)";

if ($conn->query($sql) === TRUE) {
    echo "URL column added successfully";
} else {
    echo "Error adding URL column: " . $conn->error;
}

$conn->close();
?>
