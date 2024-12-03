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

// Fetch schedules based on route_id
$route_id = $_GET['route_id'];
$date = $_GET['date'];

$sql = "SELECT schedule_id, departure_time, arrival_time 
        FROM Schedules 
        WHERE route_id = ? 
          AND DATE(departure_time) = ?
        ORDER BY departure_time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $route_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode($schedules);

$stmt->close();
$conn->close();
?>
