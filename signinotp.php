<?php
session_start();
require 'vendor/autoload.php';  // Ensure PHPMailer is installed via Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'generate') {
        $email = $_POST['email'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;

            // Send OTP to the user's email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'deepakjoshy17@gmail.com';
                $mail->Password   = 'qqzq rwul sjoh flnp';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('your_email@gmail.com', 'Waterway');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body    = 'Your OTP code is: <strong>' . $otp . '</strong><br><br>Please enter this code on the registration page to complete the process.';

                $mail->send();
                echo 'OTP has been sent to your email.';
            } catch (Exception $e) {
                echo 'OTP could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            echo 'Invalid email address.';
        }
    }
}
?>
