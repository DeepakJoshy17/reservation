<?php
include 'db_connection.php';

if (isset($_GET['booking_id'])) {
    $booking_id = (int)$_GET['booking_id'];

    $stmt = $conn->prepare("DELETE FROM Seat_Bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    if ($stmt->execute()) {
        echo "Booking deleted successfully.";
    } else {
        echo "Error deleting booking.";
    }
    $stmt->close();
}
$conn->close();
?>
