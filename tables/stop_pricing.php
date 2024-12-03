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

$sql="CREATE TABLE IF NOT EXISTS Stop_Pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_stop_id INT NOT NULL,
    end_stop_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (start_stop_id) REFERENCES Route_Stops(stop_id),
    FOREIGN KEY (end_stop_id) REFERENCES Route_Stops(stop_id)
)";
$conn->query($sql);

echo "Tables created successfully";

$conn->close();
?>

