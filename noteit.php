<?php
// config.php - Database configuration
$host = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$database = "noteit_db";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    // Database created successfully
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($database);

// Create notes table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS notes (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_favorite BOOLEAN DEFAULT 0,
    is_archived BOOLEAN DEFAULT 0,
    user_id INT(11) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    // Table created successfully
} else {
    echo "Error creating table: " . $conn->error;
}

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT 'default.jpg'
)";

if ($conn->query($sql) === TRUE) {
    // Table created successfully
} else {
    echo "Error creating table: " . $conn->error;
}

// Insert a demo user if no users exist
$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Hash password
    $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, name) VALUES ('vince', '$hashed_password', 'Vince')";
    $conn->query($sql);
}
?>