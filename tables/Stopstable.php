<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create Route_Stops table
$sql = "CREATE TABLE IF NOT EXISTS Route_Stops (
    stop_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    location VARCHAR(255) NOT NULL,
    stop_order INT NOT NULL,
    km DECIMAL(6, 2) NOT NULL DEFAULT 0,
    FOREIGN KEY (route_id) REFERENCES Routes(route_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'Route_Stops' created successfully or already exists.";
} else {
    echo "Error creating 'Route_Stops' table: " . $conn->error;
}

// Create Stop_Pricing table
$sql = "CREATE TABLE IF NOT EXISTS Stop_Pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_stop_id INT NOT NULL,
    end_stop_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (start_stop_id) REFERENCES Route_Stops(stop_id),
    FOREIGN KEY (end_stop_id) REFERENCES Route_Stops(stop_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'Stop_Pricing' created successfully or already exists.";
} else {
    echo "Error creating 'Stop_Pricing' table: " . $conn->error;
}

// Optional: Alter Route_Stops table to add km column if not already added
$sql = "ALTER TABLE Route_Stops 
        ADD COLUMN IF NOT EXISTS km DECIMAL(6, 2) NOT NULL DEFAULT 0";

if ($conn->query($sql) === TRUE) {
    echo "Column 'km' added successfully to Route_Stops table.";
} else {
    echo "Error adding 'km' column: " . $conn->error;
}

// Close connection
$conn->close();
?>
