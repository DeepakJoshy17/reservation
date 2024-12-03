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

// 1. Users Table
$sql = "CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    address TEXT,
    role VARCHAR(50) NOT NULL DEFAULT 'User'
)";
$conn->query($sql);

// 2. Boats Table
$sql = "CREATE TABLE IF NOT EXISTS Boats (
    boat_id INT AUTO_INCREMENT PRIMARY KEY,
    boat_name VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Active'
)";
$conn->query($sql);

// 3. Routes Table
$sql = "CREATE TABLE IF NOT EXISTS Routes (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    route_name VARCHAR(255) NOT NULL,
    start_location VARCHAR(255) NOT NULL,
    end_location VARCHAR(255) NOT NULL
)";
$conn->query($sql);

// 4. Route_Stops Table
$sql = "CREATE TABLE IF NOT EXISTS Route_Stops (
    stop_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    location VARCHAR(255) NOT NULL,
    stop_order INT NOT NULL,
    km DECIMAL(6, 2) NOT NULL DEFAULT 0,
    FOREIGN KEY (route_id) REFERENCES Routes(route_id)
)";
$conn->query($sql);

// 5. Schedules Table
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

// 6. Seats Table
$sql = "CREATE TABLE IF NOT EXISTS Seats (
    seat_id INT AUTO_INCREMENT PRIMARY KEY,
    boat_id INT NOT NULL,
    seat_number VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'Regular',
    FOREIGN KEY (boat_id) REFERENCES Boats(boat_id)
)";
$conn->query($sql);

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

$conn->query($sql);

// Create Payments table
$sql = "CREATE TABLE IF NOT EXISTS Payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(50) NOT NULL DEFAULT 'Pending',
    transaction_id VARCHAR(255)
)";
$conn->query($sql);

// 9. Cancellations Table
$sql = "CREATE TABLE IF NOT EXISTS Cancellations (
    cancellation_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    cancellation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reason TEXT,
    FOREIGN KEY (booking_id) REFERENCES Seat_Bookings(booking_id)
)";
$conn->query($sql);

// SQL to create Admin_Logs table
$sql = "CREATE TABLE IF NOT EXISTS Admin_Logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES Users(user_id) ON DELETE CASCADE
);"; // Added semicolon here
$conn->query($sql);

// SQL statements to create tables
$sql = "CREATE TABLE Route_Stop_Times (
    route_id INT,
    stop_id INT,
    arrival_time TIME,
    PRIMARY KEY (route_id, stop_id),
    FOREIGN KEY (route_id) REFERENCES Routes(route_id),
    FOREIGN KEY (stop_id) REFERENCES Route_Stops(stop_id)
)";
$conn->query($sql);

$sql="CREATE TABLE IF NOT EXISTS Stop_Pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_stop_id INT NOT NULL,
    end_stop_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (start_stop_id) REFERENCES Route_Stops(stop_id),
    FOREIGN KEY (end_stop_id) REFERENCES Route_Stops(stop_id)
)";
// Create Tickets table with a foreign key
$sql = "CREATE TABLE IF NOT EXISTS Tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(255),
    amount DECIMAL(10, 2), -- Amount for the ticket
    user_id INT, -- User ID foreign key
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
)";
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



$conn->query($sql);

echo "Tables created successfully";

$conn->close();
?>
