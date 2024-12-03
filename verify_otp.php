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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    // Handle OTP verification
    if ($_POST['otp'] == $_SESSION['otp']) {
        // OTP is correct, proceed with registration
        $name = $_SESSION['signup_data']['name'];
        $email = $_SESSION['signup_data']['email'];
        $phone_number = $_SESSION['signup_data']['phone_number'];
        $address = $_SESSION['signup_data']['address'];
        $password = password_hash($_SESSION['signup_data']['password'], PASSWORD_BCRYPT);
        $role = $_SESSION['signup_data']['role'];

        // Insert user data into the database
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, address, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $phone_number, $address, $password, $role);

        if ($stmt->execute()) {
            // Send confirmation email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; 
                $mail->SMTPAuth   = true; 
                $mail->Username   = 'your_email@gmail.com'; // Update with your email
                $mail->Password   = 'your_email_password'; // Update with your email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('no-reply@waterway.com', 'Waterway');
                $mail->addAddress($email); 

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Registration Confirmation';
                $mail->Body    = "Dear $name,<br><br>Thank you for registering with Waterway. Your email has been successfully registered.<br><br>Best regards,<br>Waterway Team";

                $mail->send();
                echo "Registration successful. A confirmation email has been sent to $email.";
            } catch (Exception $e) {
                echo "Registration successful, but failed to send confirmation email. Mailer Error: {$mail->ErrorInfo}";
            }

            // Clean up session data
            unset($_SESSION['otp']);
            unset($_SESSION['signup_data']);
        } else {
            $_SESSION['signup_error'] = "Registration failed. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['otp_error'] = "Invalid OTP. Please try again.";
        header("Location: signupuserhtml.php");
        exit();
    }
} else {
    // Redirect if neither generate nor verify OTP
    header("Location: signupuserhtml.php");
    exit();
}

$conn->close();
?>
