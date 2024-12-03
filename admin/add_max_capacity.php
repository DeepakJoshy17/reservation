<?php
include 'db_connection.php';
session_start(); // Ensure the session is started to access admin_id

if (isset($_GET['boat_id'])) {
    $boat_id = (int)$_GET['boat_id'];
    $seat_count_result = $conn->query("SELECT COUNT(*) as seat_count FROM Seats WHERE boat_id = $boat_id");
    $seat_count = $seat_count_result->fetch_assoc()['seat_count'];

    $capacity_result = $conn->query("SELECT capacity FROM Boats WHERE boat_id = $boat_id");
    $capacity = $capacity_result->fetch_assoc()['capacity'];

    if ($seat_count < $capacity) {
        $seats_to_add = $capacity - $seat_count;
        $stmt = $conn->prepare("INSERT INTO Seats (boat_id, seat_number, type) VALUES (?, ?, 'Regular')");

        for ($i = 1; $i <= $seats_to_add; $i++) {
            $seat_number = ($seat_count + $i);
            $stmt->bind_param("is", $boat_id, $seat_number);
            $stmt->execute();
        }
        $stmt->close();
        
        // Log the action
        $admin_id = $_SESSION['admin_id']; // Get the admin ID from session
        $action = "Added seats to max capacity";
        $description = "Added $seats_to_add seats for Boat ID: $boat_id";

        // Prepare the log statement
        $log_stmt = $conn->prepare("INSERT INTO Admin_Logs (admin_id, action, description, timestamp) VALUES (?, ?, ?, NOW())");
        $log_stmt->bind_param("iss", $admin_id, $action, $description);
        $log_stmt->execute();
        $log_stmt->close();

        echo "Seats added to maximum capacity.";
    } else {
        echo "Boat is already at full capacity.";
    }
}
?>
