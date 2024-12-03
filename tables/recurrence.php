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

// Create Payments table
$sql = "CREATE TABLE Recurrence (
    recurrence_id INT AUTO_INCREMENT PRIMARY KEY,
    boat_id INT NOT NULL,
    route_id INT NOT NULL,
    status ENUM('Scheduled', 'Unscheduled') NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    time_interval VARCHAR(10) NOT NULL, -- E.g., 'P1D' for daily
    FOREIGN KEY (boat_id) REFERENCES Boats(boat_id),
    FOREIGN KEY (route_id) REFERENCES Routes(route_id)
);";

if ($conn->query($sql) === TRUE) {
    echo "Seat_Bookings table created successfully.<br>";
} else {
    echo "Error creating Seat_Bookings table: " . $conn->error . "<br>";
}

$conn->close();
?>
