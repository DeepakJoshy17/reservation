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

// SQL statements to create tables
$sql = "CREATE TABLE Route_Stop_Times (
    route_id INT,
    stop_id INT,
    arrival_time TIME,
    PRIMARY KEY (route_id, stop_id),
    FOREIGN KEY (route_id) REFERENCES Routes(route_id),
    FOREIGN KEY (stop_id) REFERENCES Route_Stops(stop_id))";
$conn->query($sql);

echo "Tables created successfully";

$conn->close();
?>
