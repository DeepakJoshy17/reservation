<?php
include 'db_connection.php';

if (isset($_GET['route_id'])) {
    $route_id = $_GET['route_id'];
    $query = "SELECT stop_id, location FROM Route_Stops WHERE route_id = ? ORDER BY location";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $stops = [];
    while ($row = $result->fetch_assoc()) {
        $stops[] = $row;
    }

    echo json_encode($stops);
    $stmt->close();
}
?>
