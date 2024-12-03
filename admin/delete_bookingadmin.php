<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $booking_id = intval($data['booking_id']);

    $stmt = $conn->prepare("UPDATE Seat_Bookings SET payment_status = 'Cancelled' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Your booking has been cancelled.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cancellation failed.']);
    }

    $stmt->close();
    $conn->close();
}
?>

