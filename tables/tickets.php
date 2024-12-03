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

// Create Tickets table with a foreign key
$sql = "CREATE TABLE IF NOT EXISTS Tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(255),
    amount DECIMAL(10, 2), -- Amount for the ticket
    user_id INT, -- User ID foreign key
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Tickets table created successfully";
} else {
    echo "Error creating Tickets table: " . $conn->error;
}

$conn->close();
?>

