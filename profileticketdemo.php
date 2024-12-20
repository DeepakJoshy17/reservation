<?php
session_start();
require 'vendor/autoload.php'; // Autoload Composer packages

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Mpdf\Mpdf; // Import mPDF
use PHPMailer\PHPMailer\PHPMailer; // Import PHPMailer classes

// Email sending function
function sendEmail($to, $subject, $body, $pdfContent = null) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'deepakjoshy17@gmail.com';
        $mail->Password   = 'qqzq rwul sjoh flnp'; // **Security Note:** Use environment variables in production
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@waterway.com', 'Waterway Reservation');
        $mail->addAddress($to); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Add PDF attachment if provided
        if ($pdfContent) {
            $mail->addStringAttachment($pdfContent, 'ticket.pdf', 'base64', 'application/pdf');
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Function to convert number to alphabetic representation (e.g., 1 => A, 27 => AA)
function numberToAlphabetic($num) {
    $result = '';
    while ($num > 0) {
        $mod = ($num - 1) % 26;
        $result = chr(65 + $mod) . $result;
        $num = intval(($num - $mod) / 26);
    }
    return $result;
}

// Check if ticket_id and user_id are set in the URL
if (!isset($_GET['ticket_id']) || !isset($_GET['user_id'])) {
    header("Location: userhome.php");
    exit();
}

$ticket_id = $_GET['ticket_id'];
$user_id = $_GET['user_id'];

// Validate ticket_id and user_id (basic validation)
if (!is_numeric($ticket_id) || !is_numeric($user_id)) {
    echo "Invalid ticket ID or user ID.";
    exit();
}

// Fetch ticket details from the database
include 'db_connection.php';

$bookings = [];
$total_amount = 0;
$boat_name = '';
$arrival_time = '';
$departure_time = '';
$user_email = '';
$user_name = '';
$schedule_details = '';
$system_date = date('Y-m-d'); // Initialize system_date

// Fetch ticket details based on the passed ticket ID and user ID
$stmt = $conn->prepare("SELECT booking_id, amount FROM Tickets WHERE ticket_id = ? AND user_id = ?");
if (!$stmt) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    die("An error occurred. Please try again later.");
}
$stmt->bind_param("si", $ticket_id, $user_id);
$stmt->execute();
$ticket_result = $stmt->get_result();
$ticket_data = $ticket_result->fetch_assoc();
$stmt->close();

if (!$ticket_data) {
    echo "No ticket found for the given ID.";
    exit();
}

$booking_ids = explode(',', $ticket_data['booking_id']);
$total_amount = $ticket_data['amount'];

// Fetch booking details for each booking ID
foreach ($booking_ids as $booking_id) {
    $stmt = $conn->prepare("
        SELECT 
            B.booking_id, 
            U.name, 
            U.email, 
            B.user_id, 
            BO.boat_name, 
            S.seat_number, 
            B.schedule_id, 
            SCH.arrival_time, 
            SCH.departure_time, 
            RS.location AS start_stop, 
            RE.location AS end_stop
        FROM Seat_Bookings B
        JOIN Users U ON B.user_id = U.user_id
        JOIN Boats BO ON B.boat_id = BO.boat_id
        JOIN Schedules SCH ON B.schedule_id = SCH.schedule_id
        JOIN Seats S ON B.seat_id = S.seat_id
        JOIN Route_Stops RS ON B.start_stop_id = RS.stop_id
        JOIN Route_Stops RE ON B.end_stop_id = RE.stop_id
        WHERE B.booking_id = ?
    ");
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        continue; // Skip this booking_id
    }
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking_result = $stmt->get_result();
    $booking_data = $booking_result->fetch_assoc();
    $stmt->close();

    if ($booking_data) {
        $bookings[] = $booking_data;
        $user_email = $booking_data['email'];
        $user_name = $booking_data['name'];
        $boat_name = $booking_data['boat_name'];
        $arrival_time = $booking_data['arrival_time'];
        $departure_time = $booking_data['departure_time'];

        if (empty($schedule_details)) {
            $schedule_details = "{$booking_data['start_stop']} to {$booking_data['end_stop']} ({$booking_data['departure_time']} to {$booking_data['arrival_time']})";
        }
    }
}

// Ensure there is at least one booking
if (empty($bookings)) {
    echo "No booking details found.";
    exit();
}

// Create a comma-separated string of all booking IDs
$booking_ids_string = implode(',', $booking_ids); // Combine all booking IDs

// Check if the ticket has already been generated for these booking IDs
$stmt = $conn->prepare("SELECT COUNT(*) FROM Tickets WHERE booking_id = ?");
if (!$stmt) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    die("An error occurred. Please try again later.");
}
$stmt->bind_param("s", $booking_ids_string);
$stmt->execute();
$stmt->bind_result($ticket_exists);
$stmt->fetch();
$stmt->close();



// Prepare the data for the QR code
$ticket_id_display = implode(', ', array_map('numberToAlphabetic', $booking_ids)); // For display purposes
$qrData = json_encode([
    'ticket_id' => $booking_ids_string,
    'user_name' => $user_name,
    'boat_name' => $boat_name,
    'seat_numbers' => implode(', ', array_column($bookings, 'seat_number')),
    'start_stop' => $bookings[0]['start_stop'],
    'end_stop' => $bookings[0]['end_stop']
]);

// Generate QR Code with Correct Error Correction Level
$qrResult = Builder::create()
    ->writer(new PngWriter())
    ->data($qrData)
    ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
    ->errorCorrectionLevel(ErrorCorrectionLevel::High) // Use specific Error Correction Level class
    ->size(300) // Increased size for better readability
    ->margin(10)
    ->build();

$qrImageData = $qrResult->getString(); // PNG image data

// Function to generate PDF
function generatePDF($user_name, $user_email, $boat_name, $schedule_details, $arrival_time, $departure_time, $total_amount, $system_date, $qrImageData, $ticket_id_display, $booking_ids_string, $bookings) {
    $mpdf =new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A5', // Set a smaller page format (A5 size for ticket style)
        'default_font_size' => 13, // Decrease the default font size for more content fit
        'default_font' => 'Arial',
        'margin_left' => 5, // Smaller left margin
        'margin_right' => 5, // Smaller right margin
        'margin_top' => 5, // Smaller top margin
        'margin_bottom' => 0, // Smaller bottom margin
        'margin_header' => 0,
        'margin_footer' => 0
    ]);

    // Include CSS styles
    $css = '
    body {
        font-family: Arial, sans-serif;
        color: #000;
        background-color: #f5f5f5;
        margin: 0; 
        padding: 0; 
    }
    .ticket-container {
        width: 100%; // Full width of the A5 paper
        background-color: #ffffff; 
        border-radius: 10px; 
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); 
        padding: 10px; 
        border: 2px solid #6c757d; 
        box-sizing: border-box; // Ensure padding is included in width
    }
    .ticket-header {
        font-weight: bold;
        text-align: center;
        margin-bottom: 5px;
    }
    .ticket-header h2 {
        color: #ff5722; 
        font-weight: bold;
        font-size: 1.2em; // Slightly smaller font for the header
        margin: 0; // Remove margins to save space
    }
    .ticket-header p {
        font-size: 0.8em; 
        color: #6c757d; 
        margin: 0; // Remove margins
    }
    .ticket-details {
        margin-bottom: 10px; 
    }
    .ticket-details h4 {
        color: #ff5722; 
        margin: 5px 0; // Add a little space to headers
        font-weight: bold; 
        border-bottom: 1px solid #ff5722; 
        padding-bottom: 2px; 
        font-size: 0.9em; // Smaller font for detail headers
    }
    .ticket-details p {
        margin: 2px 0; // Reduced margins between paragraphs
        color: #000; 
        font-size: 0.8em; 
    }
    .qr-code {
        text-align: center;
        margin: 5px 0; // Adjusted margin for spacing
        padding: 5px; 
        background-color: #f8f9fa; 
        border-radius: 10px; 
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
    }
    .qr-code img {
        width: 80px; // Adjusted QR code size for better fit
        height: 80px; 
    }
    .footer {
        text-align: center;
        margin-top: 5px; 
        font-size: 0.7em; // Slightly smaller footer font
        color: #666;
    }
    ';

    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

    // HTML Content for PDF
    $html = '
    <div class="ticket-container">
        <div class="ticket-header">
            <h2>Waterway Reservation</h2>
            <p>Your Journey Awaits!</p>
        </div>

        <div class="ticket-details">
            <h4>User Details</h4>
            <p><strong>Name:</strong> ' . htmlspecialchars($user_name) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($user_email) . '</p>
        </div>

        <div class="ticket-details">
            <h4>Booking Details</h4>
            <p><strong>Boat Name:</strong> ' . htmlspecialchars($boat_name) . '</p>
            <p><strong>Schedule:</strong> ' . htmlspecialchars($schedule_details) . '</p>
            <p><strong>Seat Number(s):</strong> ' . htmlspecialchars(implode(', ', array_column($bookings, 'seat_number'))) . '</p>
            <p><strong>Arrival Time:</strong> ' . htmlspecialchars($arrival_time) . '</p>
            <p><strong>Departure Time:</strong> ' . htmlspecialchars($departure_time) . '</p>
            <p><strong>Total Amount:</strong> ₹' . htmlspecialchars($total_amount) . '</p>
            <p><strong>Booking ID(s):</strong> ' . htmlspecialchars($ticket_id_display) . '</p>
            <p><strong>Booking Date:</strong> ' . htmlspecialchars($system_date) . '</p>
        </div>
        
        <div class="qr-code">
            <img src="data:image/png;base64,' . base64_encode($qrImageData) . '" alt="QR Code">
        </div>
        
        <div class="footer">
            Thank you for choosing Waterway Reservations!
        </div>
    </div>
    ';

    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    return $mpdf->Output('', 'S'); // Return PDF as a string
}

// Generate PDF content
$pdfContent = generatePDF(
    $user_name, 
    $user_email, 
    $boat_name, 
    $schedule_details, 
    $arrival_time, 
    $departure_time, 
    $total_amount, 
    $system_date, 
    $qrImageData, 
    $ticket_id_display, 
    $booking_ids_string,
    $bookings
);

// Prepare email details
$email_subject = "Your Ticket - Waterway Reservation";
/*$email_body = "
    <h2>Your Ticket Details</h2>
    <p><strong>Name:</strong> {$user_name}</p>
    <p><strong>Email:</strong> {$user_email}</p>
    <p><strong>Boat Name:</strong> {$boat_name}</p>
    <p><strong>Seat Number(s):</strong> " . htmlspecialchars(implode(', ', array_column($bookings, 'seat_number'))) . "</p>
    <p><strong>Schedule:</strong> {$schedule_details}</p>
    <p><strong>Arrival Time:</strong> {$arrival_time}</p>
    <p><strong>Departure Time:</strong> {$departure_time}</p>
    <p><strong>Total Amount:</strong> ₹{$total_amount}</p>
    <p><strong>Booking ID(s):</strong> {$ticket_id_display}</p>
    <p><strong>Booking Date:</strong> {$system_date}</p>
    <p>Please find your ticket attached as a PDF.</p>
";*/


// Check if the user clicked to send the PDF
if (isset($_GET['action']) && $_GET['action'] === 'send_email') {
    if (sendEmail($user_email, $email_subject, $email_body, $pdfContent)) {
        echo "<script>alert('Your ticket has been sent to your email.');</script>";
    } else {
        echo "<script>alert('There was an error sending your ticket. Please try again later.');</script>";
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Your Ticket</title>
    <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
    <link rel="stylesheet" href="slick/slick.css">
    <link rel="stylesheet" href="slick/slick-theme.css">
    <link rel="stylesheet" href="magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
   body {
            background-color: #f5f5f5; /* Light gray background */
            font-family: 'Arial', sans-serif;
            color: #000; /* Black text */
        }
    .ticket-container {
    max-width: 400px; /* Smaller card width */
    margin: 40px auto;
    background-color: #ffffff; /* White background for the ticket */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Darker gray shadow */
    padding: 10px; /* Reduced padding for smaller height */
    border: 2px solid #6c757d; /* Gray border */
}

        .ticket-header {
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px; /* Reduced margin */
        }
        .ticket-header h2 {
            color: #ff5722; /* Gray color for the header */
            font-weight: bold;
            font-size: 1.3em; /* Adjusted font size */
            margin-bottom: 3px; /* Added spacing */
        }
        .ticket-header p {
            font-size: 0.9em; /* Normal font size */
            color: #6c757d; /* Gray color for the header */
        }
        .ticket-details {
            margin-bottom: 10px; /* Reduced margin */
        }
        .ticket-details h4 {
            color: #ff5722; /* Orange theme color */
            margin-bottom: 3px;
            font-weight: bold; /* Bold headers */
            border-bottom: 1px solid #ff5722; /* Underline for emphasis */
            padding-bottom: 2px; /* Padding for spacing */
            font-size: 1em; /* Adjusted font size */
        }
        .ticket-details p {
            margin: 0;
            color: #000; /* Black for detail text */
            font-size: 0.8em; /* Smaller font size */
        }
        .qr-code {
            text-align: center;
            margin-top: 5px; /* Reduced margin */
            padding: 5px; /* Padding around the QR code section */
            background-color: #f8f9fa; /* Light gray background for QR section */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        .qr-code img {
            width: 80px; /* Adjusted QR code size */
            height: 80px;
        }
        .footer {
            text-align: center;
            margin-top: 5px; /* Reduced margin */
            font-size: 0.8em;
            color: #666;
        }
        .cancel-button, .home-button, .email-button {
            display: block;
            width: 160px; /* Adjusted button width */
            margin: 5px auto; /* Centered buttons */
            padding: 6px 0; /* Padding for buttons */
            text-align: center;
            color: #fff;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9em; /* Normal font size */
            transition: background-color 0.3s ease;
        }
        .cancel-button {
            background-color: #f8d7da; /* Light red background for cancel */
            color: #721c24; /* Dark red text for contrast */
        }
        .cancel-button:hover {
            background-color: #f5c6cb; /* Darker shade on hover */
            color:black;
        }
        .home-button {
            background-color:  #ff5722; /* Light gray background for home */
            color: white; /* Black text */
        }
        .home-button:hover {
            background-color: #d6d8db; /* Darker shade on hover */
            color:black;
        }
        .email-button {
            background-color: #e2e3e5; /* Light gray background for home */
            color: #000; /* Black text */
        }
        .email-button:hover {
            background-color: #d6d8db; /* Darker shade on hover */
        }
    </style>
</head>
<body>
   
<div class="container">
<br><br>
    <div class="ticket-container">
        <div class="ticket-header">
            <h2>Waterway Reservation</h2>
            <p>Your Journey Awaits!</p>
        </div>

        <div class="ticket-details">
            <h4>User Details</h4>
            <p><strong>Name:</strong> <?= htmlspecialchars($user_name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user_email) ?></p>
        </div>

        <div class="ticket-details">
            <h4>Booking Details</h4>
            <p><strong>Boat Name:</strong> <?= htmlspecialchars($boat_name) ?></p>
            <p><strong>Schedule:</strong> <?= htmlspecialchars($schedule_details) ?></p>
            <p><strong>Seat Number(s):</strong> <?= htmlspecialchars(implode(', ', array_column($bookings, 'seat_number'))) ?></p>
            <p><strong>Arrival Time:</strong> <?= htmlspecialchars($arrival_time) ?></p>
            <p><strong>Total Amount:</strong> ₹<?= htmlspecialchars($total_amount) ?></p>
            <p><strong>Booking ID(s):</strong> 
                <?php
                $alphabetic_ids = array_map('numberToAlphabetic', $booking_ids);
                echo htmlspecialchars(implode(', ', $alphabetic_ids));
                ?>
            </p>
            <p><strong>Booking Date:</strong> <?= htmlspecialchars($system_date) ?></p>
        </div>
        
        <div class="qr-code">
            <img src="data:image/png;base64,<?= base64_encode($qrImageData) ?>" alt="QR Code">
        </div>
        
        <div class="footer">
            <form action="" method="get">
            <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket_id) ?>">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                <input type="hidden" name="action" value="send_email">
                <button type="submit" class="email-button">Email</button>
            </form>
            <a href="cancel_booking.php" class="cancel-button">Cancel Booking</a>
            <a href="profile.php" class="home-button">Profile</a>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
<br><br>
<section>
<?php include "includes/footer1.php"; ?>
    </section>