<?php
include 'db_connection.php';
session_start(); // Ensure the session is started to access admin_id

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat_id = $_POST['seat_id'];
    $seat_number = $_POST['seat_number'];
    $type = $_POST['type'];

    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE Seats SET seat_number = ?, type = ? WHERE seat_id = ?");
    $stmt->bind_param("ssi", $seat_number, $type, $seat_id);
    
    if ($stmt->execute()) {
        // Log the action
        $admin_id = $_SESSION['admin_id']; // Get the admin ID from session
        $action = "Updated a seat";
        $description = "Updated Seat ID: {$seat_id} to Seat Number: {$seat_number}, Type: {$type}";

        // Prepare the log statement
        $log_stmt = $conn->prepare("INSERT INTO Admin_Logs (admin_id, action, description, timestamp) VALUES (?, ?, ?, NOW())");
        $log_stmt->bind_param("iss", $admin_id, $action, $description);
        $log_stmt->execute();
        $log_stmt->close();

        echo "Seat updated successfully.";
    } else {
        echo "Error updating seat.";
    }
    
    $stmt->close();
}
?>

