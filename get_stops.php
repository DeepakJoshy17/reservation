<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if admin is logged in, if not, return a JSON error
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connection.php';

// Check if route_id is set
if (isset($_GET['route_id'])) {
    $route_id = intval($_GET['route_id']);
    
    // Prepare the query to fetch stops based on the route_id
    $query = "SELECT stop_id, location FROM Route_Stops WHERE route_id = ? ORDER BY stop_order";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Database query failed']);
        exit;
    }

    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $stops = [];
    while ($row = $result->fetch_assoc()) {
        $stops[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Return the stops as JSON
    echo json_encode($stops);
} else {
    echo json_encode(['error' => 'No route_id specified']);
}
?>

