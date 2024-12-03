<?php
include 'db_connection.php';

$route_id = $_GET['route_id'] ?? '';

if ($route_id) {
    $query = "SELECT stop_id, location FROM Route_Stops WHERE route_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stops = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($stops);
    $stmt->close();
}
?>
