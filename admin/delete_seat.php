<?php
include 'db_connection.php';
session_start(); // Ensure the session is started to access admin_id

if (isset($_GET['seat_id'])) {
    $seat_id = (int)$_GET['seat_id'];

    // Fetch seat details before deletion for logging
    $seat_result = $conn->query("SELECT seat_number, boat_id FROM Seats WHERE seat_id = $seat_id");
    $seat = $seat_result->fetch_assoc();

    if ($seat) {
        // Prepare and execute the delete statement
        $stmt = $conn->prepare("DELETE FROM Seats WHERE seat_id = ?");
        $stmt->bind_param("i", $seat_id);
        
        if ($stmt->execute()) {
            // Log the action
            $admin_id = $_SESSION['admin_id']; // Get the admin ID from session
            $action = "Deleted a seat";
            $description = "Deleted Seat Number: {$seat['seat_number']} from Boat ID: {$seat['boat_id']}";

            // Prepare the log statement
            $log_stmt = $conn->prepare("INSERT INTO Admin_Logs (admin_id, action, description, timestamp) VALUES (?, ?, ?, NOW())");
            $log_stmt->bind_param("iss", $admin_id, $action, $description);
            $log_stmt->execute();
            $log_stmt->close();

            echo "Seat deleted successfully.";
        } else {
            echo "Error deleting seat.";
        }
        
        $stmt->close();
    } else {
        echo "Seat not found.";
    }
} else {
    echo "No seat ID provided.";
}
?>
