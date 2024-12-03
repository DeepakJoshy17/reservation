<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare("UPDATE Seat_Bookings SET payment_status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $payment_status, $booking_id);
    if ($stmt->execute()) {
        echo "Booking updated successfully.";
    } else {
        echo "Error updating booking.";
    }
    $stmt->close();
}
$conn->close();
?>

