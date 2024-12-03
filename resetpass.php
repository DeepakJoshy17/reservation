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
    if (isset($_POST['new_password'], $_POST['confirm_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $email = $_SESSION['reset_email'];

        // Update the user's password
        $sql = "UPDATE Users SET password=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $email);
        if ($stmt->execute()) {
            // Send confirmation email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'deepakjoshy17@gmail.com';
                $mail->Password = 'qqzq rwul sjoh flnp';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your_email@example.com', 'Waterway');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Confirmation';
                $mail->Body = 'Your password has been successfully reset. If you did not request this change, please contact support immediately.';

                $mail->send();
            } catch (Exception $e) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }

            // Clean up session variables
            unset($_SESSION['reset_otp']);
            unset($_SESSION['reset_email']);

            // Redirect to login page
            header("Location: loginuserhtml.php");
            exit(); // Ensure no further code is executed after redirection
        } else {
            echo 'Failed to update password.';
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
    <title>Reset Password</title>
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
                    <form id="resetpass-form" action="resetpass.php" method="post" class="needs-validation" novalidate onsubmit="return validatePasswords()">
                        <h3 class="form-heading">Reset Password</h3><br>
                        <div class="mb-3 form-floating">
                            <input type="password" id="new_password" name="new_password" class="form-control" placeholder=" " required>
                            <label for="new_password">Enter New Password</label>
                            <div class="invalid-feedback">Please enter a new password.</div>
                        </div>
                        <div class="mb-3 form-floating">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder=" " required>
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="invalid-feedback">Please confirm your new password.</div>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
  

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="resetpass.js"></script>
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

        // Validate that both password fields match
        function validatePasswords() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (newPassword !== confirmPassword) {
                alert('Passwords do not match.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>





