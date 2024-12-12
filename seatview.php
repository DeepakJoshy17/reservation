<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'db_connection.php';

if (isset($_SESSION['failed_seat_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['failed_seat_message'] . "</div>";
    unset($_SESSION['failed_seat_message']);
}

$_SESSION['payment_done'] = false;

$boat_id = isset($_GET['boat_id']) ? $_GET['boat_id'] : null;
$schedule_id = isset($_GET['schedule_id']) ? $_GET['schedule_id'] : null;
$start_stop_id = isset($_GET['start_stop_id']) ? $_GET['start_stop_id'] : null;
$end_stop_id = isset($_GET['end_stop_id']) ? $_GET['end_stop_id'] : null;

if ($boat_id && $schedule_id && $start_stop_id && $end_stop_id) {
    // Fetch the boat name
    $boat_name_query = "SELECT boat_name FROM Boats WHERE boat_id = ?";
    $stmt = $conn->prepare($boat_name_query);
    $stmt->bind_param("i", $boat_id);
    $stmt->execute();
    $boat_name_result = $stmt->get_result();
    $boat_name_row = $boat_name_result->fetch_assoc();
    $boat_name = $boat_name_row['boat_name'] ?? 'Unknown Boat';
    $stmt->close();

    // Fetch the start stop order
    $start_stop_order_query = "SELECT stop_order FROM Route_Stops WHERE stop_id = ?";
    $stmt = $conn->prepare($start_stop_order_query);
    $stmt->bind_param("i", $start_stop_id);
    $stmt->execute();
    $start_stop_order_result = $stmt->get_result();
    $start_stop_order_row = $start_stop_order_result->fetch_assoc();
    $start_stop_order = $start_stop_order_row['stop_order'] ?? null;
    $stmt->close();

    // Fetch the end stop order
    $end_stop_order_query = "SELECT stop_order FROM Route_Stops WHERE stop_id = ?";
    $stmt = $conn->prepare($end_stop_order_query);
    $stmt->bind_param("i", $end_stop_id);
    $stmt->execute();
    $end_stop_order_result = $stmt->get_result();
    $end_stop_order_row = $end_stop_order_result->fetch_assoc();
    $end_stop_order = $end_stop_order_row['stop_order'] ?? null;
    $stmt->close();

    // Fetch all seats for the selected boat
    $seats_query = "SELECT seat_id, seat_number, type FROM Seats WHERE boat_id = ?";
    $stmt = $conn->prepare($seats_query);
    $stmt->bind_param("i", $boat_id);
    $stmt->execute();
    $seats_result = $stmt->get_result();
    $stmt->close();

    // Fetch booked seats with their start and end stop orders
    $booked_seats_query = "
        SELECT 
            sb.seat_id, 
            rs_start.stop_order AS start_stop_order, 
            rs_end.stop_order AS end_stop_order
        FROM 
            Seat_Bookings sb
        JOIN 
            Route_Stops rs_start ON sb.start_stop_id = rs_start.stop_id
        JOIN 
            Route_Stops rs_end ON sb.end_stop_id = rs_end.stop_id
        WHERE 
            sb.schedule_id = ? 
            AND sb.payment_status != 'Cancelled'
    ";

    $stmt = $conn->prepare($booked_seats_query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $booked_seats_result = $stmt->get_result();

    $booked_seats = [];
    while ($row = $booked_seats_result->fetch_assoc()) {
        $booked_seats[] = [
            'seat_id' => $row['seat_id'],
            'start_stop_order' => $row['start_stop_order'],
            'end_stop_order' => $row['end_stop_order']
        ];
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No boat, schedule, or start/end stop selected.</div>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Search and Book Your Seat</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <style>
/* Styling for the boat container and seats */
/* Styling for the boat container and seats */
/* Styling for the boat container and seats */
/* Styling for the boat container and seats */
/* Styling for the boat container and seats */
/* Existing CSS styles remain unchanged */

.boat-name {
    color: lightgrey;
    padding: 5px;
    margin-top: 20px;
    border-radius: 5px;
    font-size: 1.5em;
    font-weight: bold;
    position: absolute;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.seat-layout {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 50px;
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
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Leftward shadow for depth */
    text-align: center;
    line-height: 40px;
    transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
}

/* Headrest using a pseudo-element */
.seat::after {
    content: '';
    position: absolute;
    top: -10px; /* Position above the seat */
    left: 50%;
    transform: translateX(-50%);
    width: 30px; /* Width of the headrest */
    height: 10px; /* Height of the headrest */
    background-color: #ccc; /* Same color as the seat */
    border-radius: 3px;
    box-shadow: -4px -4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
}

/* Existing hover, selected, and other styles remain unchanged */

.seat:hover {
    transform: translateY(-2px); /* Lift effect on hover */
    background-color: #202428; /* Original hover color */
    color: white;
    font-weight: bold;
    box-shadow: -8px -8px 16px rgba(0, 0, 0, 0.5); /* Increased shadow on hover */
}

.seat.selected {
    background-color: #3377AA; /* Original selected color */
    color: white;
    font-weight: bold;
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
}

.available {
    background-color: white; /* Original color */
    color: #202428;
    font-weight: bold;
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
}

.booked {
    background-color: lightgrey; /* Original color */
    color: white;
    font-weight: bold;
    cursor: not-allowed;
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
}

/* Wrapper for the boat and legend */
.boat-legend-wrapper {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 20px;
    margin-top: 20px;
}

.boat-container {
    position: relative;
    width: 100%;
    max-width: 600px;
    background: #f5f5f5;
    padding: 30px;
    padding-top: 120px;
    padding-bottom: 50px;
    text-align: center;
    box-shadow: -8px -8px 16px rgba(0, 0, 0, 0.5); /* Leftward shadow for depth */
    overflow: hidden;
    border-radius: 50% 50% 0 0;
}

.legend {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-top: 300px;
    margin-left: 10px;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    background: #f9f9f9; /* Original background */
    border-radius: 5px;
    padding: 5px;
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Leftward shadow for legend items */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.legend-item:hover {
    transform: translateY(-2px); /* Slight lift on hover */
    box-shadow: -8px -8px 16px rgba(0, 0, 0, 0.5); /* Increased shadow on hover */
}

.legend-box {
    width: 20px;
    height: 20px;
    margin-right: 5px;
    border-radius: 3px; /* Slight rounding for legend boxes */
}

/* Specific Legend Box Colors */
.selected-box {
    background-color: #3377AA; /* Original selected color */
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
}

.available-box {
    background-color: white; /* Original color */
    border: 1px solid #ccc;
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
}

.booked-box {
    background-color: lightgrey; /* Original color */
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
}

/* Button Styles */
.btn {
    background-color: #007bff; /* Original button color */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    cursor: pointer;
    box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Leftward shadow for buttons */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn:hover {
    transform: translateY(-2px); /* Slight lift on hover */
    box-shadow: -8px -8px 16px rgba(0, 0, 0, 0.5); /* Increased shadow on hover */
}

/* Align buttons to bottom center */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
    margin-left: -100px;
}

.gap {
    width: 20px;
}

@media (max-width: 600px) {
    .boat-container {
        width: 150%;
        max-width: 600px;
        padding: 20px;
        background: #f5f5f5;
        padding-top: 120px;
        padding-bottom: 50px;
        text-align: center;
        box-shadow: -8px -8px 16px rgba(0, 0, 0, 0.5); /* Leftward shadow */
        overflow: hidden;
        border-radius: 50% 50% 0 0;
    }

    .boat-name {
        color: lightgrey;
        padding: 5px;
        margin-top: 30px;
        border-radius: 5px;
        font-size: 1em;
        font-weight: bold;
        position: absolute;
        top: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }

    .seat {
        width: 20px;
        height: 20px;
        line-height: 20px;
        font-size: 0.8em;
        box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
    }
   /* Headrest using a pseudo-element */
.seat::after {
    content: '';
    position: absolute;
    top: -5px; /* Position above the seat */
    left: 50%;
    transform: translateX(-50%);
    width: 15px; /* Width of the headrest */
    height: 5px; /* Height of the headrest */
    background-color: #ccc; /* Same color as the seat */
    border-radius: 3px;
    box-shadow: -4px -4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
}
    .seat-layout {
        margin-top: 30px;
    }

    .legend {
        margin-top: 300px;
    }

    .legend-item {
        font-size: 0.8em;
        box-shadow: -6px -6px 12px rgba(0, 0, 0, 0.4); /* Maintain shadow */
    }

    .action-buttons {
        margin-left: 100px;
        align: center;
        flex-direction: column; /* Stack buttons vertically */
        gap: 10px; /* Adjust spacing */
    }

    .gap {
        width: 5px; /* Adjust gap width */
    }
}



    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <br><br><br>
    <div class="container mt-5">        <h2 class="tm-text-primary mb-4 tm">Seat View <?/*= htmlspecialchars($boat_id ?? '') */?></h2>
          <!-- Wrapper for boat and legend -->
        <div class="boat-legend-wrapper">
            <!-- Boat container -->
            <div class="boat-container">
                <h1 class="boat-name"><?= htmlspecialchars($boat_name) ?></h1>
                <div id="seat-container" class="seat-layout"></div>
            </div>

            <!-- Legend -->
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-box selected-box"></div>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box available-box"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box booked-box"></div>
                    <span>Booked&nbsp;&nbsp;&nbsp;</span>
                </div>
            </div>
    </div>
        <!-- Buttons aligned to bottom center -->
      
           <!-- <button id="select-all" class="btn btn-primary custom">Select All</button>
            <button id="deselect-all" class="btn btn-primary custom">Deselect All</button>--><br>
            <!-- Update the action buttons section -->
            <div class="action-buttons">
    <form id="booking-form" method="post" action="payment.php">
        <input type="hidden" name="boat_id" value="<?= htmlspecialchars($boat_id ?? '') ?>">
        <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id ?? '') ?>">
        <input type="hidden" name="start_stop_id" value="<?= htmlspecialchars($start_stop_id ?? '') ?>">
        <input type="hidden" name="end_stop_id" value="<?= htmlspecialchars($end_stop_id ?? '') ?>">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
        <input type="hidden" name="selected_seats" id="selected-seats" value="">
        
        <button type="submit" id="book-button" class="btn btn-primary custom">Book Now</button>
    </form>
</div>
<br>
<br>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const seats = <?= json_encode($seats_result->fetch_all(MYSQLI_ASSOC)) ?>;
    const bookedSeats = <?= json_encode($booked_seats) ?>;
    const startStopOrder = <?= json_encode($start_stop_order) ?>;
    const endStopOrder = <?= json_encode($end_stop_order) ?>;
    const seatContainer = document.getElementById('seat-container');
    const selectedSeats = document.getElementById('selected-seats');
    const bookingForm = document.getElementById('booking-form');
    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

    let seatCount = 0;

    seats.forEach(seat => {
        const seatElement = document.createElement('div');
        seatElement.classList.add('seat', 'available');
        
        // Check if the seat is booked with start and end stop order constraints
        const bookedSeat = bookedSeats.find(bs => bs.seat_id == seat.seat_id);
        if (bookedSeat && startStopOrder < bookedSeat.end_stop_order) {
            seatElement.classList.remove('available');
            seatElement.classList.add('booked');
            seatElement.style.cursor = 'not-allowed';
        }
        if (bookedSeat && endStopOrder <= bookedSeat.start_stop_order) {
            seatElement.classList.remove('booked');
            seatElement.classList.add('available');
            seatElement.style.cursor = 'allowed';
        }

        seatElement.dataset.seatId = seat.seat_id;
        seatElement.textContent = seat.seat_number;
        seatElement.addEventListener('click', function () {
            if (this.classList.contains('available')) {
                this.classList.toggle('selected');
            }
            updateSelectedSeats();
        });

        seatContainer.appendChild(seatElement);
        seatCount++;

        // Add a gap after every 3 seats for layout
        if (seatCount % 3 === 0) {
            const gap = document.createElement('div');
            gap.classList.add('gap');
            seatContainer.appendChild(gap);
        }
    });

    function updateSelectedSeats() {
        const selected = Array.from(document.querySelectorAll('.seat.selected'))
            .map(seat => seat.dataset.seatId)
            .join(',');
        selectedSeats.value = selected;
    }

    bookingForm.addEventListener('submit', function (e) {
        // Validate that at least one seat is selected
        if (!selectedSeats.value) {
            e.preventDefault();
            alert('Please select at least one seat before proceeding.');
            return;
        }

        // Check if the user is logged in
        if (!isLoggedIn) {
            e.preventDefault();
            window.location.href = 'notloggedin.php';
        }
    });
});
</script>
</body>
</html>




 