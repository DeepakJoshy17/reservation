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

// SQL to create Admin_Logs table
$sql = "CREATE TABLE IF NOT EXISTS Admin_Logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES Users(user_id) ON DELETE CASCADE
);"; // Added semicolon here

if ($conn->query($sql) === TRUE) {
    echo "Admin_Logs table created successfully.<br>"; // Corrected message
} else {
    echo "Error creating Admin_Logs table: " . $conn->error . "<br>";
}

$conn->close();
?>
