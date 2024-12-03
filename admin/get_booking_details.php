<?php
include 'db_connection.php';

if (isset($_GET['booking_id'])) {
    $booking_id = (int)$_GET['booking_id'];
    $stmt = $conn->prepare("SELECT * FROM Seat_Bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
    $stmt->close();
}
$conn->close();
?>

