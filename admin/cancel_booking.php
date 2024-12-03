<?php
session_start();
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Check if POST request has booking_id
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    exit;
}

$booking_id = intval($data['booking_id']);

// Step 1: Fetch the booking details along with the user email
$query = "SELECT sb.*, u.email AS user_email, sb.seat_id 
          FROM Seat_Bookings sb 
          JOIN Users u ON sb.user_id = u.user_id 
          WHERE sb.booking_id = $booking_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    exit;
}

$booking = $result->fetch_assoc();
$user_email = $booking['user_email']; // Get user's email from the fetched booking
$seat_id = $booking['seat_id']; // Get the seat ID from the booking
$start_stop_id = $booking['start_stop_id']; // Get the start stop ID
$end_stop_id = $booking['end_stop_id']; // Get the end stop ID

// Step 2: Fetch the price from Stop_Pricing table
$pricing_query = "SELECT price FROM Stop_Pricing WHERE start_stop_id = $start_stop_id AND end_stop_id = $end_stop_id";
$pricing_result = $conn->query($pricing_query);

if ($pricing_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Pricing not found for the selected stops.']);
    exit;
}

$pricing = $pricing_result->fetch_assoc();
$price = $pricing['price'];
$refund_amount = $price * 0.75; // Calculate 75% of the price

// Step 3: Update payment status to 'Cancelled' instead of deleting
$update_query = "UPDATE Seat_Bookings SET payment_status = 'Cancelled' WHERE booking_id = $booking_id";
if ($conn->query($update_query)) {

    // Step 4: Insert cancellation details into the Cancellations table
    $insert_query = "INSERT INTO Cancellations (booking_id, cancellation_date)
                     VALUES ($booking_id, NOW())";

    if ($conn->query($insert_query)) {
        
        // Step 5: Send cancellation email to the user
        require 'vendor/autoload.php'; // Include PHPMailer library

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true; 
            $mail->Username   = 'deepakjoshy17@gmail.com'; // Your email
            $mail->Password   = 'qqzq rwul sjoh flnp'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('deepakjoshy17@gmail.com', 'Booking Cancellation'); // Use the same email as Username
            $mail->addAddress($user_email); // User's email from the booking

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Booking Cancellation Confirmation';
            $mail->Body    = "Your booking has been successfully cancelled.<br>
                              Seat Number: $seat_id<br>
                              Refund Amount: ₹" . number_format($refund_amount, 2) . "<br>
                              Thank you for using our service.";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Booking cancelled and email sent.']);
            
            // Step 6: Log the admin action
            $admin_id = $_SESSION['admin_id']; // Get admin ID from session
            
            // Define action and description for logging
            $action = "Cancellation"; // Represents the type of action
            $description = "Cancelled booking ID: $booking_id. Refund Amount: ₹" . number_format($refund_amount, 2); // Detailed description
            
            // Insert log entry
            $log_query = "INSERT INTO Admin_Logs (admin_id, action, description, timestamp) 
                          VALUES ($admin_id, '$action', '$description', NOW())";
            $conn->query($log_query); // Log the action
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to log cancellation.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
}

$conn->close();
?>










