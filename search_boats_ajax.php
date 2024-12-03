<?php
// search_boats_ajax.php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if needed (e.g., for user authentication)
session_start();

// Include database connection
include 'db_connection.php';

// Initialize variables
$schedule_date = '';
$start_stop_id = '';
$end_stop_id = '';
$no_boats_message = '';
$boats_html = '';

// Check if the request is via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data
    $schedule_date = isset($_POST['schedule_date']) ? $_POST['schedule_date'] : '';
    $start_stop_id = isset($_POST['start_stop']) ? intval($_POST['start_stop']) : 0;
    $end_stop_id = isset($_POST['end_stop']) ? intval($_POST['end_stop']) : 0;

    // Validate input
    if ($schedule_date && $start_stop_id && $end_stop_id) {
        // Get route IDs and stop orders for the start and end stops
        $stmt = $conn->prepare("
            SELECT rs1.route_id AS start_route_id, rs1.stop_order AS start_order, 
                   rs2.route_id AS end_route_id, rs2.stop_order AS end_order
            FROM Route_Stops rs1
            JOIN Route_Stops rs2 ON rs1.route_id = rs2.route_id
            WHERE rs1.stop_id = ? AND rs2.stop_id = ?
        ");
        $stmt->bind_param("ii", $start_stop_id, $end_stop_id);
        $stmt->execute();
        $route_ids_result = $stmt->get_result();
        $route_ids = $route_ids_result->fetch_assoc();
        $stmt->close();

        // Check if both stops are on the same route and in the correct order
        if ($route_ids && $route_ids['start_route_id'] === $route_ids['end_route_id'] && $route_ids['start_order'] < $route_ids['end_order']) {
            $route_id = $route_ids['start_route_id'];

            // Fetch location of the start stop
            $location_stmt = $conn->prepare("SELECT location FROM Route_Stops WHERE stop_id = ?");
            $location_stmt->bind_param("i", $start_stop_id);
            $location_stmt->execute();
            $location_result = $location_stmt->get_result();
            $start_stop_location = $location_result->fetch_assoc()['location'];
            $location_stmt->close();

            // Fetch boats available on this route and schedule date with arrival time calculation
            $stmt = $conn->prepare("
                SELECT s.schedule_id, b.boat_id, b.boat_name,
                       TIME(ADDTIME(s.departure_time, 
                          (SELECT SUM(rst.arrival_time) 
                           FROM Route_Stop_Times rst 
                           WHERE rst.route_id = s.route_id 
                             AND rst.stop_id = ? 
                           GROUP BY rst.route_id, rst.stop_id)
                       )) AS calculated_arrival_time
                FROM Boats b
                JOIN Schedules s ON b.boat_id = s.boat_id
                WHERE s.route_id = ? AND DATE(s.arrival_time) = ?
            ");
            $stmt->bind_param("iis", $start_stop_id, $route_id, $schedule_date);
            $stmt->execute();
            $boats_result = $stmt->get_result();
            $stmt->close();

            if ($boats_result->num_rows > 0) {
                // Start building the HTML for available boats
                $boats_html .= '<h2 class="mb-4">Available Boats</h2><div class="row">';

                while ($boat = $boats_result->fetch_assoc()) {
                    $boats_html .= '
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">' . htmlspecialchars($boat['boat_name']) . '</h5>
                                    <p class="card-text">Arrival Time at ' . htmlspecialchars($start_stop_location) . ': ' . htmlspecialchars($boat['calculated_arrival_time']) . '</p>
                                </div>
                                <div class="card-footer">
                                    <form method="get" action="seatview.php">
                                        <input type="hidden" name="boat_id" value="' . htmlspecialchars($boat['boat_id']) . '">
                                        <input type="hidden" name="schedule_id" value="' . htmlspecialchars($boat['schedule_id']) . '">
                                        <input type="hidden" name="start_stop_id" value="' . htmlspecialchars($start_stop_id) . '">
                                        <input type="hidden" name="end_stop_id" value="' . htmlspecialchars($end_stop_id) . '">
                                        <button type="submit" class="btn btn-primary custom">View Seats</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    ';
                }

                $boats_html .= '</div>';
            } else {
                $no_boats_message = "No boats available for the selected stops and schedule date.";
            }
        } else {
            $no_boats_message = "No boats available for the selected stops.";
        }
    } else {
        $no_boats_message = "Please provide all required fields.";
    }

    // Prepare the response
    if (!empty($no_boats_message)) {
        echo '<div class="alert alert-warning">' . htmlspecialchars($no_boats_message) . '</div>';
    } else {
        echo $boats_html;
    }
} else {
    // Invalid request method
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>
