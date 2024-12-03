<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "You must be logged in to perform this action.";
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        add_schedule();
        break;
    case 'edit':
        edit_schedule();
        break;
    case 'remove':
        remove_schedule();
        break;
    case 'fetch_schedules':
        fetch_schedules();
        break;
    default:
        echo "Invalid action.";
        break;
}

function add_schedule() {
    global $conn;
    $boat_id = $_POST['boat_id'];
    $route_id = $_POST['route_id'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];

    $query = "SELECT * FROM Schedules WHERE boat_id = ? AND route_id = ? AND departure_time = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $boat_id, $route_id, $departure_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "A schedule with the same boat, route, and departure time already exists.";
    } else {
        $query = "INSERT INTO Schedules (boat_id, route_id, departure_time, arrival_time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $boat_id, $route_id, $departure_time, $arrival_time);

        if ($stmt->execute()) {
            echo "Schedule added successfully.";
        } else {
            echo "Error adding schedule: " . $stmt->error;
        }

        $stmt->close();
    }
}

function edit_schedule() {
    global $conn;
    $schedule_id = $_POST['schedule_id'];
    $new_departure_time = $_POST['new_departure_time'];
    $new_arrival_time = $_POST['new_arrival_time'];

    $query = "UPDATE Schedules SET departure_time = ?, arrival_time = ? WHERE schedule_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $new_departure_time, $new_arrival_time, $schedule_id);

    if ($stmt->execute()) {
        echo "Schedule updated successfully.";
    } else {
        echo "Error updating schedule: " . $stmt->error;
    }

    $stmt->close();
}

function remove_schedule() {
    global $conn;
    $schedule_id = $_POST['schedule_id'];

    $query = "DELETE FROM Schedules WHERE schedule_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);

    if ($stmt->execute()) {
        echo "Schedule removed successfully.";
    } else {
        echo "Error removing schedule: " . $stmt->error;
    }

    $stmt->close();
}

function fetch_schedules() {
    global $conn;
    $boat_id = $_POST['boat_id'] ?? '';
    $route_id = $_POST['route_id'] ?? '';

    if ($boat_id && $route_id) {
        $query = "SELECT schedule_id, departure_time, arrival_time FROM Schedules WHERE boat_id = ? AND route_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $boat_id, $route_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $options = '';
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value=\"{$row['schedule_id']}\">ID: {$row['schedule_id']} | Departure: {$row['departure_time']} | Arrival: {$row['arrival_time']}</option>";
        }

        echo $options;
        $stmt->close();
    }
}
?>
