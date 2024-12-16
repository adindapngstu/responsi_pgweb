<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "diy_universitas";

try {
    // Create connection
    $conn = new mysqli($host, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }

    // Select the database
    $conn->select_db($database);

    // Create universities table with correct column names
    $sql = "CREATE TABLE IF NOT EXISTS database_univ (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Nama VARCHAR(255) NOT NULL,
        Keterangan TEXT,
        Latitude DOUBLE NOT NULL,
        Longitude DOUBLE NOT NULL,
        website_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$conn->query($sql)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Insert sample data if table is empty
    $result = $conn->query("SELECT COUNT(*) as count FROM database_univ");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        $sample_data = [
            [
                'Nama' => 'Universitas Gadjah Mada',
                'Keterangan' => 'Universitas Negeri',
                'Latitude' => -7.771537,
                'Longitude' => 110.377665,
                'website_url' => 'https://ugm.ac.id'
            ],
            [
                'Nama' => 'Universitas Negeri Yogyakarta',
                'Keterangan' => 'Universitas Negeri',
                'Latitude' => -7.777165,
                'Longitude' => 110.387193,
                'website_url' => 'https://uny.ac.id'
            ],
            [
                'Nama' => 'Universitas Islam Indonesia',
                'Keterangan' => 'Universitas Swasta',
                'Latitude' => -7.759147,
                'Longitude' => 110.409865,
                'website_url' => 'https://uii.ac.id'
            ]
        ];

        foreach ($sample_data as $data) {
            $sql = "INSERT INTO database_univ (Nama, Keterangan, Latitude, Longitude, website_url) 
                   VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdds", 
                $data['Nama'],
                $data['Keterangan'],
                $data['Latitude'],
                $data['Longitude'],
                $data['website_url']
            );
            if (!$stmt->execute()) {
                throw new Exception("Error inserting data: " . $stmt->error);
            }
            $stmt->close();
        }
    }

    echo "Database setup completed successfully!";

} catch (Exception $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
