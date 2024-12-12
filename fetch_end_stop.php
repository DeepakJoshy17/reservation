<?php
// fetch_stops.php
include 'db_connection.php';

if (isset($_POST['start_stop_id'])) {
    $start_stop_id = intval($_POST['start_stop_id']);

    // Get the route_id for the selected start stop
    $query = $conn->prepare("SELECT route_id FROM Route_Stops WHERE stop_id = ?");
    $query->bind_param("i", $start_stop_id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        $route_id = $row['route_id'];

        // Fetch stops on the same route
        $stops_query = $conn->prepare("SELECT stop_id, location FROM Route_Stops WHERE route_id = ? AND stop_id != ?");
        $stops_query->bind_param("ii", $route_id, $start_stop_id);
        $stops_query->execute();
        $stops_result = $stops_query->get_result();

        $stops = [];
        while ($stop = $stops_result->fetch_assoc()) {
            $stops[] = $stop;
        }

        echo json_encode($stops);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
