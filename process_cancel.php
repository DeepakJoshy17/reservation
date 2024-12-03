<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

// Check if POST request has booking_ids and ticket_id
if (!isset($_POST['booking_ids']) || !isset($_POST['ticket_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    exit;
}

$booking_ids = explode(',', $_POST['booking_ids']); // Array of booking IDs
$ticket_id = intval($_POST['ticket_id']);
$total_deduction = 0; // Variable to store total price deduction

// Step 1: Fetch the booking details for the selected bookings
$placeholders = implode(',', array_fill(0, count($booking_ids), '?'));
$stmt = $conn->prepare("
    SELECT sb.start_stop_id, sb.end_stop_id, sb.seat_id, sb.user_id
    FROM Seat_Bookings sb
    WHERE sb.booking_id IN ($placeholders)
");
$stmt->bind_param(str_repeat('s', count($booking_ids)), ...$booking_ids);
$stmt->execute();
$bookings_result = $stmt->get_result();

// Step 2: Calculate total price to deduct based on pricing for each booking
while ($booking = $bookings_result->fetch_assoc()) {
    $start_stop_id = $booking['start_stop_id'];
    $end_stop_id = $booking['end_stop_id'];

    // Fetch the price from Stop_Pricing table
    $pricing_query = "SELECT price FROM Stop_Pricing WHERE start_stop_id = ? AND end_stop_id = ?";
    $pricing_stmt = $conn->prepare($pricing_query);
    $pricing_stmt->bind_param("ii", $start_stop_id, $end_stop_id);
    $pricing_stmt->execute();
    $pricing_result = $pricing_stmt->get_result();

    if ($pricing_result->num_rows > 0) {
        $price_row = $pricing_result->fetch_assoc();
        $total_deduction += $price_row['price']; // Sum the prices for the canceled bookings
    } else {
        echo json_encode(['success' => false, 'message' => 'Pricing not found for one of the selected stops.']);
        exit;
    }
}

// Step 3: Cancel each booking by updating the payment status
foreach ($booking_ids as $booking_id) {
    $delete_query = "UPDATE Seat_Bookings SET payment_status = 'Cancelled' WHERE booking_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("s", $booking_id);
    $delete_stmt->execute();
}

// Step 4: Update the amount in the Tickets table
$ticket_query = "SELECT amount, booking_id FROM Tickets WHERE ticket_id = ?";
$ticket_stmt = $conn->prepare($ticket_query);
$ticket_stmt->bind_param("i", $ticket_id);
$ticket_stmt->execute();
$ticket_result = $ticket_stmt->get_result();
$ticket = $ticket_result->fetch_assoc();

if ($ticket) {
    $new_amount = $ticket['amount'] - $total_deduction;

    // Update the Tickets table with the new amount
    $update_ticket_query = "UPDATE Tickets SET amount = ? WHERE ticket_id = ?";
    $update_ticket_stmt = $conn->prepare($update_ticket_query);
    $update_ticket_stmt->bind_param("di", $new_amount, $ticket_id);
    if ($update_ticket_stmt->execute()) {
        // Step 5: Remove canceled booking IDs from the ticket's booking_id field
        $current_booking_ids = explode(',', $ticket['booking_id']);
        $remaining_booking_ids = array_diff($current_booking_ids, $booking_ids); // Remove canceled IDs

        // Update the booking_id field in the Tickets table
        $updated_booking_ids = implode(',', $remaining_booking_ids);
        $update_booking_query = "UPDATE Tickets SET booking_id = ? WHERE ticket_id = ?";
        $update_booking_stmt = $conn->prepare($update_booking_query);
        $update_booking_stmt->bind_param("si", $updated_booking_ids, $ticket_id);
        if ($update_booking_stmt->execute()) {
            $_SESSION['cancel_message'] = 'Bookings cancelled and amount updated.'; // Store message in session
            header("Location: profile.php"); // Redirect back to user profile
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update the booking IDs in the ticket.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update the ticket amount.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ticket not found.']);
}

// Optional: Log the cancellation in the Cancellations table
foreach ($booking_ids as $booking_id) {
    $insert_query = "INSERT INTO Cancellations (booking_id, cancellation_date) VALUES (?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("s", $booking_id);
    $insert_stmt->execute();
}

$conn->close();


