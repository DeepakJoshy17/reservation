<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true; 
        $mail->Username   = 'deepakjoshy17@gmail.com'; // Your Gmail address
        $mail->Password   = 'qqzq rwul sjoh flnp'; // Your Gmail password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@waterway.com', 'Waterway');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Test sending an OTP email
$testEmail = 'appujoshysindhu17@gmail.com'; // Replace with a test email address
$testSubject = 'Test OTP Email';
$testBody = 'This is a test email. If you see this, the email sending function is working.';
if (sendEmail($testEmail, $testSubject, $testBody)) {
    echo 'Email sent successfully.';
} else {
    echo 'Failed to send email.';
}
?>
