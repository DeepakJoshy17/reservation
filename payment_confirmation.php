<?php
// payment_confirmation.php

session_start();

// Optionally, you can check if booking_ids and payment_id are set
if (!isset($_SESSION['booking_ids']) || !isset($_SESSION['payment_id'])) {
    // Redirect to home or show an error
    header("Location: userhome.php");
    exit();
}

// Check if ticket has already been generated
if (!isset($_SESSION['ticket_generated'])) {
    $_SESSION['ticket_generated'] = false; // Initialize ticket generation state
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5;url=ticket_view.php"> <!-- Redirects after 5 seconds -->
    <title>Payment Confirmation</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
        }
        h4 {
            color: #3377AA; /* Primary color */
            margin-bottom: 20px;
            font-size: 2.5em; /* Larger font size */
        }
        .alert {
            font-size: 1.2em; /* Larger font size */
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            background-color: #3377AA; /* Light green background */
            color: white; /* Dark green text */
        }
        .loading-animation {
            margin: 20px 0;
            font-size: 1.2em; /* Larger font size for message */
            color: #555; /* Neutral text color */
        }
        .spinner {
            border: 6px solid rgba(0, 0, 0, 0.1);
            border-left-color: #3377AA; /* Primary color for spinner */
            border-radius: 50%;
            width: 60px; /* Larger spinner */
            height: 60px; /* Larger spinner */
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 1em; /* Slightly larger font */
            color: #666;
        }
        .msgh{
            color:white;
            weight:bold;
        }
        .msg{
            color:#3377AA;
            weight:bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h5 class="msgh">Payment Processing</h5>
        <div class="alert alert-success">
            Your payment is being processed! Please wait while we generate your tickets.
        </div>

        <div class="loading-animation">
            <div class="spinner"></div>
            <h5 class="msg">Generating your ticket...</h5>
        </div>
        <div class="footer">
            <h5 class="msg">Thank you for choosing us!</h5>
        </div>
    </div>
</body>
</html>








