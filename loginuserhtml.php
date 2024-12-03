<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Waterway - Login</title>
    <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
    <link rel="stylesheet" href="slick/slick.css">
    <link rel="stylesheet" href="slick/slick-theme.css">
    <link rel="stylesheet" href="magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
    <link rel="stylesheet" href="styles.css">
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
        <form id="login-form" action="loginuser.php" method="post" novalidate>
            <h3 class="form-heading">Login</h3><br>
            <?php
                if (isset($_SESSION['login_error']) && !empty($_SESSION['login_error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['login_error'] . '</div>';
                    unset($_SESSION['login_error']);
                }
                if (isset($_SESSION['otp_error']) && !empty($_SESSION['otp_error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['otp_error'] . '</div>';
                    unset($_SESSION['otp_error']);
                }
            ?>
            <div class="mb-3 form-floating">
                <input type="email" id="email" name="email" class="form-control" placeholder=" " required>
                <label for="email">Email</label>
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            <div class="mb-3 form-floating">
                <input type="password" id="password" name="password" class="form-control" placeholder=" " required>
                <label for="password">Password</label>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button type="submit" id="submitBtn" class="btn btn-outline-secondary" disabled>Login</button>
            <button type="reset" class="btn btn-outline-secondary ms-2">Reset</button>
        </form>
        <div class="text-end mt-3">
            <a href="signupuserhtml.php">Don't have an account?</a>
            <br>
            <a href="forgotpass.php">Forgot Password?</a>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="loginuser.js"></script>
</body>
</html>






