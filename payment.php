<?php
session_start(); // Ensure session is started
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if payment is already done
if (isset($_SESSION['payment_done']) && $_SESSION['payment_done']) {
    header('Location: payment_confirmation.php');
    exit();
}

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boat_id = $_POST['boat_id'];
    $schedule_id = $_POST['schedule_id'];
    $user_id = $_POST['user_id'];
    $selected_seats = $_POST['selected_seats'];
    $start_stop_id = $_POST['start_stop_id'];
    $end_stop_id = $_POST['end_stop_id'];
    $seat_ids = explode(',', $selected_seats);

    // Fetch user details
    $user_query = "SELECT email, phone_number FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    $stmt->close();

    // Fetch the price for the route
    $pricing_query = "SELECT price FROM Stop_Pricing WHERE start_stop_id = ? AND end_stop_id = ?";
    $stmt = $conn->prepare($pricing_query);
    $stmt->bind_param("ii", $start_stop_id, $end_stop_id);
    $stmt->execute();
    $pricing_result = $stmt->get_result();
    $price_row = $pricing_result->fetch_assoc();
    $price_per_seat = $price_row['price'];
    $stmt->close();

    $total_amount = $price_per_seat * count($seat_ids);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .payment-card {
            width: 100%;
            max-width: 450px; 
            padding: 20px; 
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            background-color: #fff;
            border: 1px solid #e0e4e8;
        }
        .payment-card h2 {
            font-weight: bold;
            color: #3377AA;
            text-align: center;
            margin-bottom: 0; 
            font-size: 22px; 
        }
        .form-group label {
            font-weight: 600;
            color: #555;
        }
        .form-control {
            padding: 10px; 
            font-size: 15px; 
        }
        .masked-input {
            background-color: #eaeaea; /* Light grey background */
            color: transparent; /* Hide actual input text */
            text-shadow: 0 0 0 black; /* Show shadowed text */
            letter-spacing: 2px; /* Space out letters */
            transition: all 0.3s; /* Smooth transition for blur effect */
        }
        .masked-input::placeholder {
            color: #999; /* Placeholder color */
            opacity: 1; /* Make placeholder fully opaque */
        }
        .masked-input:focus {
            color: black; /* Show input text when focused */
            text-shadow: none; /* Remove text shadow */
        }
        .btn-primary {
            width: 100%;
            font-weight: bold;
            padding: 12px;
            font-size: 16px;
            background-color: #3377AA;
            border-color: #3377AA;
        }
        .btn-primary:hover {
            background-color: #285e8c;
            border-color: #285e8c;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #7d8793;
            font-size: 14px;
            font-weight: 500;
        }
        .payment-select {
            display: flex;
            align-items: center;
            border: 1px solid #ccd1d7;
            border-radius: 8px;
            padding: 5px;
            margin-bottom: 10px;
            background: #fff;
            cursor: pointer;
        }
        .payment-select select {
            flex-grow: 1;
            border: none;
            outline: none;
            padding: 10px; /* Added padding for better appearance */
            font-size: 15px; /* Adjust font size */
        }
        .payment-select i {
            margin-right: 10px;
        }
        .payment-select .fa-credit-card {
            color: #2c3e50; /* Blue color for credit card icon */
        }
        .payment-select .fa-cc-visa {
            color: #2c3e50; /* Dark color for Visa icon */
        }
        .payment-select .fa-google-pay {
            color: #2c3e50; /* Google Pay color */
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <h2>Payment Details</h2>
        <form id="payment-form" action="process_payment.php" method="POST">
            <div class="form-group">
                <label for="user_name">User Name:</label>
                <input type="text" class="form-control" id="user_name" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile Number:</label>
                <input type="text" class="form-control" id="mobile" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="total_amount">Amount to Pay:</label>
                <input type="text" class="form-control" id="total_amount" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>" readonly>
            </div>

            <!-- Payment Method Selection -->
            <div class="form-group payment-select">
                <i id="selected-icon" class="payment-icon fab fa-google-pay fa-2x"></i>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="gpay">Google Pay</option>
                    <option value="visa">Visa Card</option>
                    <option value="creditcard">Credit Card</option>
                </select>
            </div>

            <!-- Payment Input Fields -->
            <div id="payment-inputs" style="display: show;">
                <div class="form-group">
                    <label for="payment_info" id="payment_info_label">Payment Info:</label>
                    <input type="text" class="form-control masked-input" id="payment_info" name="payment_info" placeholder="Enter Payment Information">
                </div>
            </div>

            <!-- Hidden fields to pass relevant data -->
            <input type="hidden" name="boat_id" value="<?php echo $boat_id; ?>">
            <input type="hidden" name="schedule_id" value="<?php echo $schedule_id; ?>">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="selected_seats" value="<?php echo htmlspecialchars($selected_seats); ?>">
            <input type="hidden" name="start_stop_id" value="<?php echo $start_stop_id; ?>">
            <input type="hidden" name="end_stop_id" value="<?php echo $end_stop_id; ?>">

            <button type="submit" class="btn btn-primary">Pay Now <i class="fas fa-lock"></i></button>
        </form>
        <div class="footer">
            <p>Secured Payment Gateway</p>
        </div>
    </div>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            var selectedOption = this.value;
            var paymentInputs = document.getElementById('payment-inputs');
            var paymentInfoLabel = document.getElementById('payment_info_label');
            var paymentInfoField = document.getElementById('payment_info');
            var selectedIcon = document.getElementById('selected-icon');

            // Show input field for the selected payment option
            if (selectedOption) {
                paymentInputs.style.display = 'block';
                paymentInfoLabel.innerText = selectedOption === 'gpay' ? 'Enter UPI ID:' : (selectedOption === 'visa' ? 'Enter Visa Card Number:' : 'Enter Credit Card Number:');
                paymentInfoField.placeholder = selectedOption === 'gpay' ? 'UPI ID' : 'Card Number';

                // Set icon based on selected payment method
                selectedIcon.className = '';
                if (selectedOption === 'gpay') {
                    selectedIcon.classList.add('fab', 'fa-google-pay', 'fa-2x');
                } else if (selectedOption === 'visa') {
                    selectedIcon.classList.add('fab', 'fa-cc-visa', 'fa-2x');
                } else if (selectedOption === 'creditcard') {
                    selectedIcon.classList.add('fas', 'fa-credit-card', 'fa-2x');
                }
            } else {
                paymentInputs.style.display = 'none';
                selectedIcon.className = '';
            }
        });
    </script>
</body>
</html>








<?php
}
?>
