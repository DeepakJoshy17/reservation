<?php
session_start();

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

// Ensure PHPMailer is installed via Composer
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['otp'])) {
        // Handle OTP verification
        if ($_POST['otp'] == $_SESSION['otp']) {
            // OTP is correct, proceed with registration
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone_number = $_POST['phone_number'];
            $address = $_POST['address'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $role = $_POST['role'];

            // Prepare and execute the insert statement
            $stmt = $conn->prepare("INSERT INTO Users (name, email, phone_number, address, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                // Prepare failed
                $_SESSION['signup_error'] = "Error preparing statement: " . $conn->error;
                header("Location: signupuserhtml.php");
                exit();
            }

            $stmt->bind_param("ssssss", $name, $email, $phone_number, $address, $password, $role);

            if ($stmt->execute()) {
                // Success, send confirmation email and redirect to login page
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
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Successful';
                    $mail->Body    = 'Dear ' . $name . ',<br><br>Your registration with Waterway was successful. You can now log in to your account.<br><br>Best Regards,<br>Waterway Team';

                    $mail->send();
                    $_SESSION['signup_success'] = "Registration successful. You can now log in.";
                    header("Location: loginuserhtml.php");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['signup_error'] = "Error sending email: " . $mail->ErrorInfo;
                    header("Location: signupuserhtml.php");
                    exit();
                }
            } else {
                // Error, set error message
                $_SESSION['signup_error'] = "Error executing query: " . $stmt->error;
                header("Location: signupuserhtml.php");
                exit();
            }

            $stmt->close();
        } else {
            // OTP is incorrect
            $_SESSION['otp_error'] = "Incorrect OTP. Please try again.";
            header("Location: signupuserhtml.php");
            exit();
        }
    } else {
        $_SESSION['signup_error'] = "OTP is required.";
        header("Location: signupuserhtml.php");
        exit();
    }
}

$conn->close();
?>
