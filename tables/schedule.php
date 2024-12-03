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
}// 5. Schedules Table
$sql = "CREATE TABLE IF NOT EXISTS Schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    boat_id INT NOT NULL,
    route_id INT NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Scheduled',
    FOREIGN KEY (boat_id) REFERENCES Boats(boat_id),
    FOREIGN KEY (route_id) REFERENCES Routes(route_id)
   
)";
$conn->query($sql);
if ($conn->query($sql) === TRUE) {
    echo "Payments table created successfully.<br>";
} else {
    echo "Error creating Payments table: " . $conn->error . "<br>";
}?>