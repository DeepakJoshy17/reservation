<?php
session_start();
include 'db_connection.php';

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
    case 'fetch':
        fetch_schedule();
        break;
}

function log_admin_action($admin_id, $action, $description) {
    global $conn;
    $log_query = "INSERT INTO Admin_Logs (admin_id, action, description, timestamp) 
                  VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($log_query);
    $stmt->bind_param("iss", $admin_id, $action, $description);
    $stmt->execute();
    $stmt->close();
}

function fetch_schedule() {
    global $conn;
    $schedule_id = intval($_POST['schedule_id']);
    $query = "SELECT * FROM Schedules WHERE schedule_id = $schedule_id";
    $result = $conn->query($query);
    $schedule = $result->fetch_assoc();
    echo json_encode($schedule);
}

function add_schedule() {
    global $conn;
    $boat_id = intval($_POST['boat_id']);
    $route_id = intval($_POST['route_id']);
    $departure_time = $conn->real_escape_string($_POST['departure_time']);
    $arrival_time = $conn->real_escape_string($_POST['arrival_time']);
    $status = $conn->real_escape_string($_POST['status']);
    $is_recurring = intval($_POST['recurring']);
    $admin_id = $_SESSION['admin_id']; // Get the logged-in admin's ID for logging

    // Parse the departure and arrival times into DateTime objects
    $departure_date = new DateTime($departure_time);
    $arrival_date = new DateTime($arrival_time);

    if ($is_recurring) {
        // Log a single entry for recurring schedule
        $description = "Added recurring schedule for boat $boat_id, route $route_id, starting on $departure_time.";
        log_admin_action($admin_id, 'add', $description);

        for ($i = 0; $i < 365; $i++) {
            // Clone and increment the departure and arrival times
            $new_departure = clone $departure_date;
            $new_departure->modify("+$i days");
            $new_arrival = clone $arrival_date;
            $new_arrival->modify("+$i days");

            // Format them for database insertion
            $formatted_departure = $new_departure->format('Y-m-d H:i:s');
            $formatted_arrival = $new_arrival->format('Y-m-d H:i:s');

            // Check for conflicts and add the schedule if none found
            if (!schedule_conflict($boat_id, $route_id, $formatted_departure, $formatted_arrival)) {
                $query = "INSERT INTO Schedules (boat_id, route_id, departure_time, arrival_time, status) 
                          VALUES ('$boat_id', '$route_id', '$formatted_departure', '$formatted_arrival', '$status')";
                $conn->query($query);
            }
        }
    } else {
        if (!schedule_conflict($boat_id, $route_id, $departure_time, $arrival_time)) {
            $query = "INSERT INTO Schedules (boat_id, route_id, departure_time, arrival_time, status) 
                      VALUES ('$boat_id', '$route_id', '$departure_time', '$arrival_time', '$status')";
            $conn->query($query);

            // Log the action for non-recurring schedule
            $description = "Added schedule for boat $boat_id, route $route_id, departing at $departure_time.";
            log_admin_action($admin_id, 'add', $description);
        } else {
            echo "Conflict with existing schedule.";
            exit;
        }
    }
    echo "Schedule added successfully.";
}

function edit_schedule() {
    global $conn;
    $schedule_id = intval($_POST['schedule_id']);
    $boat_id = intval($_POST['boat_id']);
    $route_id = intval($_POST['route_id']);
    $departure_time = $conn->real_escape_string($_POST['departure_time']);
    $arrival_time = $conn->real_escape_string($_POST['arrival_time']);
    $status = $conn->real_escape_string($_POST['status']);
    $admin_id = $_SESSION['admin_id']; // Get the logged-in admin's ID for logging

    if (!schedule_conflict($boat_id, $route_id, $departure_time, $arrival_time)) {
        $query = "UPDATE Schedules 
                  SET boat_id = '$boat_id', route_id = '$route_id', departure_time = '$departure_time', 
                      arrival_time = '$arrival_time', status = '$status' 
                  WHERE schedule_id = $schedule_id";
        $conn->query($query);

        // Log the action for schedule update
        $description = "Updated schedule for boat $boat_id, route $route_id, departing at $departure_time.";
        log_admin_action($admin_id, 'edit', $description);

        echo "Schedule updated successfully.";
    } else {
        echo "Conflict with existing schedule.";
    }
}

function remove_schedule() {
    global $conn;
    $schedule_id = intval($_POST['schedule_id']);
    $admin_id = $_SESSION['admin_id']; // Get the logged-in admin's ID for logging
    
    // Prepare the delete statement
    $query = "DELETE FROM Schedules WHERE schedule_id = ?";
    $stmt = $conn->prepare($query);
    
    // Bind the schedule_id parameter and execute
    $stmt->bind_param("i", $schedule_id);
    if ($stmt->execute()) {
        // Log the action for schedule removal
        $description = "Removed schedule with ID $schedule_id.";
        log_admin_action($admin_id, 'remove', $description);
        
        echo "Schedule removed successfully.";
    } else {
        // Display error if the query fails
        echo "Error: Unable to remove schedule. " . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
}

function schedule_conflict($boat_id, $route_id, $departure_time, $arrival_time) {
    global $conn;
    $query = "SELECT COUNT(*) FROM Schedules WHERE boat_id = ? AND route_id = ? AND (
        (departure_time >= ? AND departure_time < ?) OR
        (arrival_time > ? AND arrival_time <= ?) OR
        (departure_time < ? AND arrival_time > ?)
    )";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iissssss", $boat_id, $route_id, $departure_time, $arrival_time, $departure_time, $arrival_time, $departure_time, $arrival_time);
    $stmt->execute();
    $stmt->bind_result($conflict_count);
    $stmt->fetch();
    $stmt->close();

    return $conflict_count > 0;
}
?>

