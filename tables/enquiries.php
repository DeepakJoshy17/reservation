<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create Enquiries table with response fields
$sql = "CREATE TABLE Enquiries (
    enquiry_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    response TEXT DEFAULT NULL,
    response_created_at DATETIME DEFAULT NULL,
    response_status TINYINT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE SET NULL
);"; // Added semicolon here

if ($conn->query($sql) === TRUE) {
    echo "Admin_Logs table created successfully.<br>"; // Corrected message
} else {
    echo "Error creating Admin_Logs table: " . $conn->error . "<br>";
}

$conn->close();
?>
