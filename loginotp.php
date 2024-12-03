<?php
session_start();
header('Content-Type: application/json');

// Ensure the autoload file is correctly included
require 'vendor/autoload.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'generate') {
        $email = $_POST['email'];
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP(); 
                $mail->Host       = 'smtp.gmail.com'; 
                $mail->SMTPAuth   = true; 
                $mail->Username   = 'deepakjoshy17@gmail.com'; 
                $mail->Password   = 'qqzq rwul sjoh flnp'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port       = 587; 

                // Recipients
                $mail->setFrom('your_email@gmail.com', 'Waterway');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true); 
                $mail->Subject = 'Your OTP Code';
                $mail->Body    = 'Your OTP code is: <strong>' . $otp . '</strong><br><br>Please enter this code on the login page to complete the process.';

                $mail->send();
                echo json_encode(['message' => 'OTP has been sent to your email.']);
            } catch (Exception $e) {
                echo json_encode(['error' => 'OTP could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['error' => 'Invalid email address.']);
        }
    }
}
?>
