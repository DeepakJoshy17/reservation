<?php
session_start();
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Store email in session
        $_SESSION['submitted_email'] = $email;

        // Check if the email exists
        $sql = "SELECT * FROM Users WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['reset_otp'] = $otp;
            $_SESSION['reset_email'] = $email;

            // Send OTP via email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'deepakjoshy17@gmail.com'; // Your email address
                $mail->Password = 'qqzq rwul sjoh flnp'; // Your email password or app-specific password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your_email@example.com', 'Waterway'); // Set the sender's email and name
                $mail->addAddress($email); // Add the recipient's email
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body = "Your OTP for password reset is: <b>$otp</b>";

                $mail->send();
                $_SESSION['otp_success'] = 'OTP has been sent to your email.';
            } catch (Exception $e) {
                $_SESSION['otp_error'] = 'Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['email_error'] = 'This email is not registered.'; // Error message for unregistered email
        }
        header("Location: forgotpass.php");
        exit();
    } elseif (isset($_POST['otp'])) {
        $otp = $_POST['otp'];
        if ($otp == $_SESSION['reset_otp']) {
            // Redirect to password reset page
            header("Location: resetpass.php");
            exit();
        } else {
            $_SESSION['otp_error'] = 'Incorrect OTP.';
            header("Location: forgotpass.php");
            exit();
        }
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Forgot Password</title>
    <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
    <link rel="stylesheet" href="slick/slick.css">
    <link rel="stylesheet" href="slick/slick-theme.css">
    <link rel="stylesheet" href="magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
    <style>
        a {
    color: black; /* Set link color to black */
    text-decoration: none; /* Remove underline from links */
}
             body {
            display: flex; /* Enable flexbox on the body */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: 100vh; /* Full viewport height */
            margin: 0; /* Remove default margin */
            overflow-x: hidden; /* Prevent scrolling */
             }
             .background {
    position: fixed; /* Fix the background to the viewport */
    top: 0; /* Align with the top */
    right: 0; /* Align with the right */
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    background: linear-gradient(135deg, #3377AA, #2A6C99, #4793C1, #5AADE4, #3377AA);
    background-size: 400% 400%;
    animation: gradientShift 8s ease infinite;
    clip-path: polygon(150% 0, 0 0, 0 80%); /* Diagonal triangle effect */
    z-index: -1; /* Behind the container */
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}


        .logo {
            position: fixed;
            top: 20px; /* Distance from the top */
            left: 20px; /* Distance from the left */
            font-size: 24px; /* Adjust font size */
            font-weight: bold; /* Bold text */
            color: white; /* Text color */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); /* Shadow effect */
        }

        .login-form {
            background-color: #ffffff; /* Change to pure white */
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%; /* Full width */
    max-width: 400px; /* Limit maximum width */
    margin: 100px auto; /* Center vertically with margin */
    position: relative; /* Ensure stacking order above the background */
    z-index: 1; /* Above the background */
    text-shadow: none; /* Remove shadow effect for form text */
        }

        .login-form form {
            width: 100%;
        }

        .login-form input,
        .login-form select {
            border-radius: 10px;
            font-weight: bold; /* Bold font */
        }

        .login-form label {
            font-weight: bold; /* Bold font */
        }

        .login-form button {
            border-radius: 10px;
            margin-top: 10px;
            font-weight: bold; /* Bold font */
        }

        .form-heading {
            font-weight: bold;
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>

<div class="background"></div> <!-- Fixed background with moving colors -->
    <div class="logo">Waterway</div> <!-- Added website name -->
    <div class="login-form">
                    <form id="forgotpass-form" action="forgotpass.php" method="post" class="needs-validation" novalidate>
                        <h3 class="form-heading">Forgot Password</h3><br>
                        <div class="mb-3 form-floating">
                            <input type="email" id="email" name="email" class="form-control" placeholder=" " required 
                            value="<?php echo isset($_SESSION['submitted_email']) ? htmlspecialchars($_SESSION['submitted_email']) : ''; ?>">
                            <label for="email">Enter your email</label>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary w-100">Send OTP</button>
                    </form>

                    <!-- OTP verification form -->
                    <form id="otp-form" action="forgotpass.php" method="post" class="needs-validation mt-4" novalidate>
                        <div class="mb-3 form-floating">
                            <input type="text" id="otp" name="otp" class="form-control" placeholder=" " required>
                            <label for="otp">Enter OTP</label>
                            <div class="invalid-feedback">Please enter the OTP sent to your email.</div>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary w-100">Verify OTP</button>
                    </form>
                    
                    <!-- Display error messages -->
                    <?php
                        if (isset($_SESSION['email_error']) && !empty($_SESSION['email_error'])) {
                            echo '<div class="alert alert-danger mt-3" role="alert">' . $_SESSION['email_error'] . '</div>';
                            unset($_SESSION['email_error']);
                        }
                        if (isset($_SESSION['otp_error']) && !empty($_SESSION['otp_error'])) {
                            echo '<div class="alert alert-danger mt-3" role="alert">' . $_SESSION['otp_error'] . '</div>';
                            unset($_SESSION['otp_error']);
                        }
                        if (isset($_SESSION['otp_success']) && !empty($_SESSION['otp_success'])) {
                            echo '<div class="alert alert-success mt-3" role="alert">' . $_SESSION['otp_success'] . '</div>';
                            unset($_SESSION['otp_success']);
                        }
                    ?>
             </div>
    </div>



    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="loginuser.js"></script>
    <script>
        // Enable Bootstrap validation
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>



