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
// 3. Routes Table
$sql = "CREATE TABLE IF NOT EXISTS Routes (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    route_name VARCHAR(255) NOT NULL,
    start_location VARCHAR(255) NOT NULL,
    end_location VARCHAR(255) NOT NULL,
    is_return TINYINT(1) NOT NULL DEFAULT 0 -- Field to indicate if the route is a return route
)";
$conn->query($sql);