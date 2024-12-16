<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connect to MySQL without selecting a database
    $reviews_conn = new mysqli('localhost', 'root', '');
    
    if ($reviews_conn->connect_error) {
        throw new Exception("Connection failed: " . $reviews_conn->connect_error);
    }

    // Create the reviews database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS reviews";
    if (!$reviews_conn->query($sql)) {
        throw new Exception("Error creating database: " . $reviews_conn->error);
    }
    
    // Select the reviews database
    if (!$reviews_conn->select_db('reviews')) {
        throw new Exception("Error selecting database: " . $reviews_conn->error);
    }

    // Create the reviews table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        university_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$reviews_conn->query($sql)) {
        throw new Exception("Error creating table: " . $reviews_conn->error);
    }

    echo "Success! The reviews database and table have been created.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Don't close the connection as it will be used in other files
// $reviews_conn->close();
?>
