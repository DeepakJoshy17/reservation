<?php
require 'vendor/autoload.php'; // Load PHPMailer's autoloader
include 'db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_name = $data['user_name'];

    // Fetch user's email
    $stmt = $conn->prepare("SELECT email FROM Users WHERE name = ?");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $user_email = $result['email'];
    $stmt->close();

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'deepakjoshy17@gmail.com';
        $mail->Password   = 'qqzq rwul sjoh flnp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('deepakjoshy17@gmail.com', 'Admin');
        $mail->addAddress($user_email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Cancellation Notification';
        $mail->Body    = 'Dear ' . htmlspecialchars($user_name) . ',<br>Your booking has been cancelled by the admin.<br>Thank you.';

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Cancellation email sent.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
    }
}
$conn->close();
?>
