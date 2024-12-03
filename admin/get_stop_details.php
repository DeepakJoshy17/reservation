<?php
include 'db_connection.php';

$stop_id = $_GET['stop_id'] ?? '';

if ($stop_id) {
    $query = "SELECT Route_Stops.stop_id, location, stop_order, arrival_time 
              FROM Route_Stops
              JOIN Route_Stop_Times ON Route_Stops.stop_id = Route_Stop_Times.stop_id 
              WHERE Route_Stops.stop_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $stop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stop = $result->fetch_assoc();
    echo json_encode($stop);
    $stmt->close();
}
?>

