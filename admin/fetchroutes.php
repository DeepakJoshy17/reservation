<?php
require 'db_connection.php';

$start_stop = isset($_GET['start_stop']) ? $_GET['start_stop'] : '';
$end_stop = isset($_GET['end_stop']) ? $_GET['end_stop'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

if ($start_stop && $end_stop && $date) {
    // Fetch routes that include both start and end stops
    $query = "
        SELECT DISTINCT r.route_id, r.route_name
        FROM Routes r
        JOIN Route_Stops rs1 ON r.route_id = rs1.route_id
        JOIN Route_Stops rs2 ON r.route_id = rs2.route_id
        WHERE rs1.stop_id = ? AND rs2.stop_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $start_stop, $end_stop);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<h2>Available Boats</h2>';
        while ($row = $result->fetch_assoc()) {
            $route_id = $row['route_id'];
            echo "<h3>{$row['route_name']}</h3>";

            // Fetch schedules for this route
            $query_schedules = "
                SELECT s.schedule_id, s.boat_id, b.boat_name, s.departure_time, s.arrival_time
                FROM Schedules s
                JOIN Boats b ON s.boat_id = b.boat_id
                WHERE s.route_id = ? AND s.departure_time >= ? AND s.arrival_time <= ?
            ";
            $stmt_schedules = $conn->prepare($query_schedules);
            $stmt_schedules->bind_param("iss", $route_id, $date, $date);
            $stmt_schedules->execute();
            $result_schedules = $stmt_schedules->get_result();

            if ($result_schedules->num_rows > 0) {
                echo '<ul class="list-group">';
                while ($schedule = $result_schedules->fetch_assoc()) {
                    echo "<li class='list-group-item'>
                        Boat: {$schedule['boat_name']} - Departure: {$schedule['departure_time']} - Arrival: {$schedule['arrival_time']}
                    </li>";
                }
                echo '</ul>';
            } else {
                echo "<p>No schedules available for this route on the selected date.</p>";
            }
        }
    } else {
        echo "<p>No routes found for the selected stops.</p>";
    }
    $stmt->close();
}
$conn->close();
?>
