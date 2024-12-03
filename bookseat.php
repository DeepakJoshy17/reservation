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

// Fetch available seats based on schedule_id and stops
$schedule_id = $_GET['schedule_id'];
$start_stop_id = $_GET['start_stop_id'];
$end_stop_id = $_GET['end_stop_id'];

$sql = "SELECT s.seat_id, s.seat_number, s.type
        FROM Seats s
        LEFT JOIN Seat_Bookings sb ON s.seat_id = sb.seat_id
          AND sb.schedule_id = ?
          AND ((sb.start_stop_id <= ? AND sb.end_stop_id >= ?) OR (sb.start_stop_id >= ? AND sb.end_stop_id <= ?))
        WHERE sb.seat_id IS NULL
        ORDER BY s.seat_number";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $schedule_id, $start_stop_id, $end_stop_id, $start_stop_id, $end_stop_id);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = $row;
}

echo json_encode($seats);

$stmt->close();
$conn->close();
?>
