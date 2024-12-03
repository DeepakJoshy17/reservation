<?php
require 'db_connection.php';

// Fetch all stops
$query = "SELECT stop_id, location FROM Route_Stops ORDER BY stop_order";
$result = $conn->query($query);

$stops = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stops[] = $row;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($stops);

$conn->close();
?>
