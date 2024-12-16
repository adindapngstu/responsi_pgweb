<?php
// Prevent any output before headers
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

// Prevent any HTML output
ob_clean();

try {
    // Connect to the existing database
    $conn = new mysqli('localhost', 'root', '', 'diy_universitas');
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create reviews table if it doesn't exist
    $create_table = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        univ_id INT NOT NULL,
        rating INT NOT NULL,
        review TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (univ_id) REFERENCES database_univ(id)
    )";
    
    if (!$conn->query($create_table)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get POST data
        $univ_id = isset($_POST['univ_id']) ? (int)$_POST['univ_id'] : 0;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $review = isset($_POST['review']) ? trim($_POST['review']) : '';

        // Log received data for debugging
        error_log("Received POST data - univ_id: $univ_id, rating: $rating, review: $review");

        // Validation
        if ($univ_id <= 0) throw new Exception("Invalid university ID");
        if ($rating < 1 || $rating > 5) throw new Exception("Rating must be between 1 and 5");
        if (empty($review)) throw new Exception("Review cannot be empty");

        // Insert review
        $stmt = $conn->prepare("INSERT INTO reviews (univ_id, rating, review) VALUES (?, ?, ?)");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        $stmt->bind_param("iis", $univ_id, $rating, $review);
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

        $stmt->close();
        echo json_encode(['success' => true]);
        exit;
    } 
    else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $univ_id = isset($_GET['univ_id']) ? (int)$_GET['univ_id'] : 0;
        
        // Log received data for debugging
        error_log("Received GET request for univ_id: $univ_id");
        
        if ($univ_id <= 0) throw new Exception("Invalid university ID");

        $stmt = $conn->prepare("SELECT rating, review, created_at FROM reviews WHERE univ_id = ? ORDER BY created_at DESC");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        $stmt->bind_param("i", $univ_id);
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }

        $stmt->close();
        echo json_encode($reviews);
        exit;
    }

    $conn->close();
} catch (Exception $e) {
    error_log("Error in save_review.php: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>
