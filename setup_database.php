<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'diy_universitas');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create reviews table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    univ_id INT NOT NULL,
    rating INT NOT NULL,
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Reviews table created/verified successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// Check if table exists and show structure
$result = $conn->query("DESCRIBE reviews");
if ($result) {
    echo "\n\nTable structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }
} else {
    echo "\nError checking table structure: " . $conn->error;
}

$conn->close();
?>
