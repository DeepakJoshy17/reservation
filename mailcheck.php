<?php
require 'vendor/autoload.php';  // Ensure PHPMailer is installed via Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                 
    $mail->Host       = 'smtp.gmail.com';           // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                        // Enable SMTP authentication
    $mail->Username   = 'deepakjoshy17@gmail.com';      // SMTP username
    $mail->Password   = 'qqzq rwul sjoh flnp';         // SMTP password or app password if using 2FA
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port       = 587;                         // TCP port to connect to

    //Recipients
    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress('appujoshysindhu17@gmail.com', 'Recipient Name'); // Add a recipient

    //Content
    $mail->isHTML(true);                            // Set email format to HTML
    $mail->Subject = 'SMTP Test Email';
    $mail->Body    = 'This is a test email sent using PHPMailer to check SMTP configuration.';

    $mail->SMTPDebug = 2; // Output debugging information
    $mail->send();
    echo 'Test email has been sent successfully.';
} catch (Exception $e) {
    echo "Test email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
