<?php
session_start();
// Enable error reporting for debugging (remove in production)
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


// Include your database connection file
include 'db_connection.php'; // Ensure this file is present and contains a valid connection

// Check for the cancellation message
if (isset($_SESSION['cancel_message'])) {
    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['cancel_message']) . '</div>';
    unset($_SESSION['cancel_message']); // Clear the message after displaying it
}
$user_id = $_SESSION['user_id']; // Assuming you set this during login

// Initialize $user_data
$user_data = [];

// Fetch user data
$sql_user = "SELECT name, email, phone_number, address FROM Users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);

if ($stmt_user === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt_user->bind_param("i", $user_id);

if (!$stmt_user->execute()) {
    die("Error executing query: " . $stmt_user->error);
}

$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
}

$stmt_user->close();

// Initialize variables for form handling
$update_success = "";
$update_error = "";

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Retrieve and sanitize user inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Basic validation
    if (empty($name) || empty($email)) {
        $update_error = "Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_error = "Invalid email format.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $update_error = "Passwords do not match.";
    } else {
        // Prepare the SQL statement
        if (!empty($password)) {
            // If password is being updated
            $sql_update = "UPDATE Users SET name = ?, email = ?, phone_number = ?, address = ?, password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql_update);
            // Hash the password before storing
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bind_param("sssssi", $name, $email, $phone_number, $address, $hashed_password, $user_id);
        } else {
            // If password is not being updated
            $sql_update = "UPDATE Users SET name = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("ssssi", $name, $email, $phone_number, $address, $user_id);
        }

        if ($stmt->execute()) {
            $update_success = "Profile updated successfully.";
            // Refresh user data
            $user_data['name'] = htmlspecialchars($name);
            $user_data['email'] = htmlspecialchars($email);
            $user_data['phone_number'] = htmlspecialchars($phone_number);
            $user_data['address'] = htmlspecialchars($address);
            // Update session variables if necessary
            $_SESSION['user_name'] = $name;
        } else {
            // Handle errors (e.g., duplicate email)
            if ($conn->errno === 1062) { // Duplicate entry
                $update_error = "Email already exists.";
            } else {
                // Log the actual error and show a generic message
                error_log("Profile Update Error: " . $conn->error);
                $update_error = "An error occurred while updating your profile. Please try again later.";
            }
        }

        $stmt->close();
    }
}

// Fetch user's tickets
$sql_tickets = "
    SELECT 
        t.ticket_id,
        t.booking_id,
        t.amount,
        sb.seat_id,
        sb.booking_date,
        b.boat_id,
        sc.schedule_id,
        rs_start.location AS start_stop,
        rs_end.location AS end_stop
    FROM 
        Tickets t
    JOIN 
        Seat_Bookings sb ON t.booking_id = sb.booking_id
    JOIN 
        Boats b ON sb.boat_id = b.boat_id
    JOIN 
        Schedules sc ON sb.schedule_id = sc.schedule_id
    JOIN 
        Route_Stops rs_start ON sb.start_stop_id = rs_start.stop_id
    JOIN 
        Route_Stops rs_end ON sb.end_stop_id = rs_end.stop_id
    WHERE 
        sb.user_id = ?
    ORDER BY 
        sb.booking_date DESC
";

$stmt_tickets = $conn->prepare($sql_tickets);

if ($stmt_tickets === false) {
    error_log("SQL Prepare Error (Tickets): " . $conn->error);
    die("An error occurred while fetching your tickets. Please try again later.");
}

$stmt_tickets->bind_param("i", $user_id);

if (!$stmt_tickets->execute()) {
    error_log("SQL Execute Error (Tickets): " . $stmt_tickets->error);
    die("An error occurred while fetching your tickets. Please try again later.");
}

$result_tickets = $stmt_tickets->get_result();
// Fetch user's enquiries
$sql_enquiries = "SELECT enquiry_id, name, message, created_at, response, response_created_at FROM Enquiries WHERE user_id = ?";
$stmt_enquiries = $conn->prepare($sql_enquiries);

if ($stmt_enquiries === false) {
    error_log("SQL Prepare Error (Enquiries): " . $conn->error);
    die("An error occurred while fetching your enquiries. Please try again later.");
}

$stmt_enquiries->bind_param("i", $user_id);

if (!$stmt_enquiries->execute()) {
    error_log("SQL Execute Error (Enquiries): " . $stmt_enquiries->error);
    die("An error occurred while fetching your enquiries. Please try again later.");
}

$result_enquiries = $stmt_enquiries->get_result();  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>User Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <style>
        .container {
            margin-top: 0px; /* Reduced margin for a tighter layout */
            margin-bottom: 0px;
        }

        .nav-tabs .nav-link.active {
            background-color: #f8f9fa;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .form-section,
        .bookings-section {
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px; /* Added rounded corners */
            background-color: #f8f9fa; /* Light background for sections */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .table-responsive {
            margin-top: 20px;
        }

        @media (max-width: 576px) {
            .nav-tabs .nav-link {
                text-align: center; /* Center-align for mobile */
            }

            .form-group {
                margin-bottom: 15px; /* Increase margin for mobile spacing */
            }
        }
        /*enquiry style*/
        .direct-chat-msg {
        display: flex;
        align-items: flex-start; /* Aligns the user icon and message vertically */
        margin-bottom: 15px; /* Spacing between messages */
    }

    .direct-chat-infos {
        flex: 1; /* Takes up available space */
    }

    .direct-chat-name {
        font-weight: bold; /* Makes the username bold */
    }

    .direct-chat-text {
        background-color: #f8f9fa; /* Light background for the message */
        border-radius: 5px; /* Rounded corners */
        padding: 10px; /* Padding for the message text */
        margin-top: 5px; /* Space between name/timestamp and message */
    }

    .direct-chat-timestamp {
        font-size: 0.9em; /* Slightly smaller font for the timestamp */
        color: #6c757d; /* Muted color for the timestamp */
    }

    .direct-chat-msg i {
        margin-right: 10px; /* Space between icon and name */
        color: #007bff; /* Color for the user icon */
    }

    .response-text {
        background-color: #d1ecf1; /* Light background for the response */
        border-radius: 5px; /* Rounded corners */
        padding: 10px; /* Padding for the response text */
        margin-top: 5px; /* Space above the response */
    }

    .response-timestamp {
        font-size: 0.9em; /* Smaller font for the response timestamp */
        color: #6c757d; /* Muted color for the response timestamp */
    }

    .user-group {
        margin-top: 20px;
        border: 1px solid #007bff;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer; /* Cursor changes to pointer on hover */
    }

    .chat-details {
        display: none; /* Hide chat details by default */
        margin-top: 10px;
    }

    .notification {
        font-weight: bold;
        color: red; /* Highlight the notification count */
    }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
  <br><br><br><br>
    <div class="container">
        <h3 class="mb-4 text-center">User Profile</h3>

        <!-- Display Success or Error Messages -->
        <?php if ($update_success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($update_success) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if ($update_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($update_error) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs" id="profileTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-info-tab" data-toggle="tab" href="#profile-info" role="tab" aria-controls="profile-info" aria-selected="true">
                    <i class="fas fa-user"></i> Profile Information
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="bookings-tab" data-toggle="tab" href="#bookings" role="tab" aria-controls="bookings" aria-selected="false">
                    <i class="fas fa-ticket-alt"></i> My Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="enquiries-tab" data-toggle="tab" href="#enquiries" role="tab" aria-controls="enquiries" aria-selected="false">
                    <i class="fas fa-comments"></i> My Enquiries
                </a>
            </li>
        </ul>


        <div class="tab-content" id="profileTabContent">
            <!-- Profile Information Tab -->
            <div class="tab-pane fade show active" id="profile-info" role="tabpanel" aria-labelledby="profile-info-tab">
                <div class="form-section">
                    <h4>Edit Profile</h4>
                    <form method="POST" action="profile.php">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name"><i class="fas fa-user"></i> Name</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($user_data['name'] ?? '') ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($user_data['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="phone_number"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user_data['phone_number'] ?? '') ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address"><i class="fas fa-home"></i> Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user_data['address'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="password"><i class="fas fa-lock"></i> New Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-info btn-sm mt-2">Update Profile</button>
                    </form>
                </div>
            </div>

            <!-- View Tickets Tab -->
<!-- View Tickets Tab -->
<div class="tab-pane fade" id="bookings" role="tabpanel" aria-labelledby="tickets-tab">
    <div class="bookings-section">
        <h4>Your Tickets</h4>
        <?php if ($result_tickets->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($ticket = $result_tickets->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <strong>Booking Date:</strong> <?= htmlspecialchars($ticket['booking_date']) ?><br>
                        <strong>Ticket ID:</strong> <?= htmlspecialchars($ticket['ticket_id']) ?><br>
                        <strong>Amount:</strong> ₹<?= htmlspecialchars($ticket['amount']) ?><br>
                        <a href="view_ticket.php?ticket_id=<?= htmlspecialchars($ticket['ticket_id']) ?>&user_id=<?= htmlspecialchars($user_id) ?>" class="btn btn-info btn-sm mt-2">
                            View Ticket
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                You have no tickets booked.
            </div>
        <?php endif; ?>
        </div>
        </div>
     <div class="tab-pane fade" id="enquiries" role="tabpanel" aria-labelledby="enquiries-tab">
    <div class="enquiries-section">
        <?php if ($result_enquiries->num_rows > 0): ?>
            <h4 class="section-title">Your Enquiries</h4>
            <div class="table-responsive">
                <?php while ($enquiry = $result_enquiries->fetch_assoc()): ?>
                    <div class="direct-chat-msg">
                        <i class="fas fa-user"></i> <!-- Replace with the actual user icon if necessary -->
                        <div class="direct-chat-infos">
                            <span class="direct-chat-name"><?= htmlspecialchars($enquiry['name']) ?></span>
                            <span class="direct-chat-timestamp"><?= htmlspecialchars($enquiry['created_at']) ?></span>
                            <div class="direct-chat-text">
                                <?= htmlspecialchars($enquiry['message']) ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($enquiry['response'])): ?>
                        <div class="direct-chat-msg">
                            <i class="fas fa-user-shield"></i> <!-- Replace with the response icon if necessary -->
                            <div class="direct-chat-infos">
                                <span class="direct-chat-name">Admin</span>
                                <span class="response-timestamp"><?= htmlspecialchars($enquiry['response_created_at']) ?></span>
                                <div class="response-text">
                                    <?= htmlspecialchars($enquiry['response']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="alert alert-warning">No enquiries found.</p>
        <?php endif; ?>
    </div>
</div>


<br><br>

    <!-- Include footer -->
    <?php include 'includes/footer1.php'; ?>
    

</body>
</html>


                    

