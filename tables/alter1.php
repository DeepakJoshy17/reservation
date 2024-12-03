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
$sql = "CREATE TABLE IF NOT EXISTS Seat_Bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    user_id INT NOT NULL,
    seat_id INT NOT NULL,
    start_stop_id INT NOT NULL,
    end_stop_id INT NOT NULL,
    booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    payment_id INT,
    boat_id INT NOT NULL,  -- New boat_id field
    FOREIGN KEY (schedule_id) REFERENCES Schedules(schedule_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (seat_id) REFERENCES Seats(seat_id),
    FOREIGN KEY (start_stop_id) REFERENCES Route_Stops(stop_id),
    FOREIGN KEY (end_stop_id) REFERENCES Route_Stops(stop_id),
    FOREIGN KEY (payment_id) REFERENCES Payments(payment_id),
    FOREIGN KEY (boat_id) REFERENCES Boats(boat_id)  -- New foreign key constraint
)";

if ($conn->query($sql) === TRUE) {
    echo "Seat_Bookings table created successfully.<br>";
} else {
    echo "Error creating Seat_Bookings table: " . $conn->error . "<br>";
}

$conn->close();
?>
