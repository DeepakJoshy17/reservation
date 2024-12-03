<?php
session_start();
include 'db_connection.php';

if (!isset($_GET['ticket_id'])) {
    echo "Invalid ticket ID.";
    exit;
}

$ticket_id = intval($_GET['ticket_id']);

// Step 1: Fetch the ticket and its bookings
$query = "SELECT booking_id, amount, user_id FROM Tickets WHERE ticket_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Ticket not found.";
    exit;
}

$ticket = $result->fetch_assoc();
$booking_ids = explode(',', $ticket['booking_id']); // Split the booking IDs into an array

// Step 2: Fetch booking details based on booking IDs
$placeholders = implode(',', array_fill(0, count($booking_ids), '?'));
$stmt = $conn->prepare("
    SELECT sb.booking_id, sb.seat_id, sb.payment_status, s.seat_number 
    FROM Seat_Bookings sb
    JOIN Seats s ON sb.seat_id = s.seat_id
    WHERE sb.booking_id IN ($placeholders)
");
$stmt->bind_param(str_repeat('s', count($booking_ids)), ...$booking_ids); // Assuming booking_id is VARCHAR
$stmt->execute();
$bookings_result = $stmt->get_result();

if ($bookings_result->num_rows === 0) {
    echo "No bookings found for this ticket.";
    exit;
}

// Display the bookings
$bookings = [];
while ($row = $bookings_result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #e9ecef; /* Light background color */
            font-family: Arial, sans-serif; /* Font style */
            margin: 0;
            padding: 20px;
        }

        .boat-name {
            color: lightgrey;
            padding: 5px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
            z-index: 10;
        }

        .seat {
            display: inline-block;
            width: 40px;
            height: 40px;
            margin: 5px;
            cursor: pointer;
            position: relative;
            border: 2px solid #ccc;
            border-radius: 5px;
            background-color: white; /* Original color */
            box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Shadow for depth */
            text-align: center;
            line-height: 40px;
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .seat:hover {
            transform: translateY(-2px); /* Lift effect on hover */
            background-color: #202428; /* Original hover color */
            color: white;
            font-weight: bold;
            box-shadow: -8px -8px 16px rgba(0, 0, 0, 0.5); /* Increased shadow on hover */
        }

        .seat.selected {
            background-color: #3377AA; /* Selected color */
            color: white;
            font-weight: bold;
            box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
        }

        /* Button Styles */
        .btn {
            background-color: #007bff; /* Button color */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Shadow */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px); /* Lift effect on hover */
            box-shadow: -8px -8px 16px rgba(0, 0, 0, 0.5); /* Increased shadow on hover */
        }

        /* Centering the seat layout */
        .seat-layout {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Center seats horizontally */
            margin-top: 50px; /* Space above the seat layout */
        }

        /* Media query for responsiveness */
        @media (max-width: 600px) {
            .seat {
                width: 30px; /* Adjust seat size for smaller screens */
                height: 30px;
                line-height: 30px;
            }

            .boat-name {
                font-size: 1.2em; /* Smaller font size */
            }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<br><br><br><br>

<div class="container">

    
    <div class="boat-name">Cancel Booking for Ticket ID: <?= htmlspecialchars($ticket_id) ?></div> <!-- Optional boat name -->
    
    <form id="cancelForm" action="process_cancel.php" method="post">
        <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket_id) ?>">
        <label class="d-block text-center">Select seats to cancel:</label>
        
        <div class="seat-layout" id="seatLayout">
            <?php foreach ($bookings as $booking): ?>
                <div class="seat" data-booking-id="<?= htmlspecialchars($booking['booking_id']) ?>">
                    <?= htmlspecialchars($booking['seat_number']) ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <input type="hidden" name="booking_ids" id="booking_ids" value="">
        
        <div class="text-center" style="margin-top: 20px;">
            <button type="submit" class="btn btn-danger" id="submitBtn">Cancel Selected Bookings</button>
        </div>
    </form>
</div>

<script>
     const seats = document.querySelectorAll('.seat');
    const submitBtn = document.getElementById('submitBtn');
    
    seats.forEach(seat => {
        seat.addEventListener('click', function() {
            this.classList.toggle('selected');
            const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'))
                                       .map(seat => seat.getAttribute('data-booking-id'));
            document.getElementById('booking_ids').value = selectedSeats.join(',');
        });
    });

    submitBtn.addEventListener('click', function(event) {
        const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'));
        if (selectedSeats.length === 0) {
            event.preventDefault(); // Prevent form submission
            alert("Please select at least one seat to cancel."); // Alert message
        }
    });
</script>

</body>
</html>





