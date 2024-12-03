<?php
session_start();

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Update with your database password
$dbname = "reservation"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_otp'])) {
    // Handle OTP generation
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Generate OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['signup_data'] = $_POST; // Save form data in session

    // Send OTP email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true; 
        $mail->Username   = 'deepakjoshy17@gmail.com'; // Update with your email
        $mail->Password   = 'qqzq rwul sjoh flnp'; // Update with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@waterway.com', 'Waterway');
        $mail->addAddress($email); 

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Waterway OTP Code';
        $mail->Body    = "Dear $name,<br><br>Your OTP code is $otp.<br><br>Please enter this code to complete your registration.<br><br>Thank you,<br>Waterway Team";

        $mail->send();
        // Redirect to the same page to show OTP field
        header("Location: signupuserhtml.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['signup_error'] = "Failed to send OTP. Please try again. Mailer Error: {$mail->ErrorInfo}";
        header("Location: signupuserhtml.php");
        exit();
    }
}

$conn->close();
?>
