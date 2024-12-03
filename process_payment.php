<?php
// process_payment.php

session_start();

// Include database connection and PHPMailer
include 'db_connection.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the payment was already processed
if (isset($_SESSION['payment_done']) && $_SESSION['payment_done'] === true) {
    // Redirect to confirmation page or seatview
    header("Location: payment_confirmation.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $boat_id = $_POST['boat_id'];
    $schedule_id = $_POST['schedule_id'];
    $user_id = $_POST['user_id'];
    $selected_seats = $_POST['selected_seats'];
    $start_stop_id = $_POST['start_stop_id'];
    $end_stop_id = $_POST['end_stop_id'];
    $total_amount = $_POST['total_amount'];
    $payment_method = 'Credit Card'; // Example method, can be dynamic
    $seat_ids = explode(',', $selected_seats);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Fetch user details for email
        $stmt = $conn->prepare("SELECT email FROM Users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $user_data = $user_result->fetch_assoc();
        $stmt->close();
        $user_email = $user_data['email'];

        // Fetch boat name
        $stmt = $conn->prepare("SELECT boat_name FROM Boats WHERE boat_id = ?");
        $stmt->bind_param("i", $boat_id);
        $stmt->execute();
        $boat_result = $stmt->get_result();
        $boat_data = $boat_result->fetch_assoc();
        $stmt->close();
        $boat_name = $boat_data['boat_name'];

        // Fetch schedule details
        $stmt = $conn->prepare("
            SELECT R.route_name, S.departure_time, S.arrival_time 
            FROM Schedules S 
            JOIN Routes R ON S.route_id = R.route_id 
            WHERE S.schedule_id = ?
        ");
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $schedule_result = $stmt->get_result();
        $schedule_data = $schedule_result->fetch_assoc();
        $stmt->close();
        $schedule_details = $schedule_data['route_name'] . " (" . $schedule_data['departure_time'] . " to " . $schedule_data['arrival_time'] . ")";

        $seat_numbers = []; // Array to hold seat numbers
        $booking_ids = []; // Array to hold booking IDs

        // Insert into Seat_Bookings and collect booking IDs
        foreach ($seat_ids as $seat_id) {
            // Fetch seat number
            $stmt = $conn->prepare("SELECT seat_number FROM Seats WHERE seat_id = ?");
            $stmt->bind_param("i", $seat_id);
            $stmt->execute();
            $seat_result = $stmt->get_result();
            $seat_data = $seat_result->fetch_assoc();
            $seat_number = $seat_data['seat_number'];
            $seat_numbers[] = $seat_number; // Add seat number to array
            $stmt->close();

            // Insert into Seat_Bookings (including boat_id)
            $stmt = $conn->prepare("
                INSERT INTO Seat_Bookings (schedule_id, user_id, seat_id, start_stop_id, end_stop_id, booking_date, payment_status, boat_id)
                VALUES (?, ?, ?, ?, ?, NOW(), 'Pending', ?)
            ");
            $stmt->bind_param("iiiiii", $schedule_id, $user_id, $seat_id, $start_stop_id, $end_stop_id, $boat_id); // Add boat_id here
            $stmt->execute();
            $booking_id = $stmt->insert_id; // Store the inserted booking ID
            $booking_ids[] = $booking_id; // Add booking ID to array
            $stmt->close();
        }

        // Insert into Payments table
        $stmt = $conn->prepare("
            INSERT INTO Payments (amount, payment_method, payment_status)
            VALUES (?, ?, 'Paid')
        ");
        $stmt->bind_param("ds", $total_amount, $payment_method); // Use total amount and payment method
        $stmt->execute();
        $payment_id = $stmt->insert_id; // Store the inserted payment ID
        $stmt->close();

        // Update each Seat_Bookings with the payment_id
        foreach ($booking_ids as $booking_id) {
            $stmt = $conn->prepare("
                UPDATE Seat_Bookings 
                SET payment_status = 'Paid', payment_id = ?
                WHERE booking_id = ?
            ");
            $stmt->bind_param("ii", $payment_id, $booking_id);
            $stmt->execute();
            $stmt->close();
        }

        // Commit transaction
        $conn->commit();

        // Set session variable to prevent repeated payments
        $_SESSION['payment_done'] = true;

        // Store booking_ids and payment_id in session for ticket generation
        $_SESSION['booking_ids'] = $booking_ids;
        $_SESSION['payment_id'] = $payment_id;

        // Send confirmation email
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'deepakjoshy17@gmail.com'; // Your email username
        $mail->Password = 'qqzq rwul sjoh flnp'; // Your email password (Use environment variables in production)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_email@example.com', 'Waterway Reservations');
        $mail->addAddress($user_email); // User's email

        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation';
        $mail->Body    = '
            <p>Dear User,</p>
            <p>Your booking was successful! Here are your booking details:</p>
            <ul>
                <li>Boat Name: ' . htmlspecialchars($boat_name) . '</li>
                <li>Schedule: ' . htmlspecialchars($schedule_details) . '</li>
                <li>Selected Seats: ' . htmlspecialchars(implode(', ', $seat_numbers)) . '</li>
                <li>Total Amount: ' . htmlspecialchars($total_amount) . '</li>
            </ul>
            <p>Thank you for choosing Waterway!</p>
        ';

        if (!$mail->send()) {
            echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        } else {
            // Redirect to confirmation page
            header("Location: payment_confirmation.php");
            exit();
        }

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error processing payment: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
}
?>






