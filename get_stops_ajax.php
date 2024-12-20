<?php
// get_stops_ajax.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $route_id = isset($_POST['route_id']) ? intval($_POST['route_id']) : 0;

    if ($route_id > 0) {
        // Fetch stops for the selected route
        $stops_result = $conn->query("SELECT stop_id, location FROM Route_Stops WHERE route_id = $route_id ORDER BY stop_order");

        $start_stops_options = ['<option value="">-- Select Start Stop --</option>'];
        $end_stops_options = ['<option value="">-- Select End Stop --</option>'];

        while ($stop = $stops_result->fetch_assoc()) {
            $start_stops_options[] = '<option value="' . htmlspecialchars($stop['stop_id']) . '">' . htmlspecialchars($stop['location']) . '</option>';
            $end_stops_options[] = '<option value="' . htmlspecialchars($stop['stop_id']) . '">' . htmlspecialchars($stop['location']) . '</option>';
        }

        // Send response back with updated dropdowns
        echo json_encode(['start_stops' => implode('', $start_stops_options), 'end_stops' => implode('', $end_stops_options)]);
    } else {
        echo json_encode(['start_stops' => '<option value="">-- Select Start Stop --</option>', 'end_stops' => '<option value="">-- Select End Stop --</option>']);
    }
}
?>
