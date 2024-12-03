<?php 
session_start();
include 'headeradmin.php'; 
include 'sidebar.php';
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

// Fetch routes and stops for filtering
$routes = $conn->query("SELECT route_id, route_name FROM Routes");
$stops = $conn->query("SELECT stop_id, location FROM Route_Stops");

// Base query for fetching bookings
$query = "SELECT b.booking_id, b.booking_date, b.payment_status, u.name AS user_name, u.email AS user_email, s.seat_number, r.route_name, rs.location AS stop_location
          FROM Seat_Bookings b
          JOIN Users u ON b.user_id = u.user_id
          JOIN Seats s ON b.seat_id = s.seat_id
          JOIN Schedules sch ON b.schedule_id = sch.schedule_id
          JOIN Routes r ON sch.route_id = r.route_id
          JOIN Route_Stops rs ON b.end_stop_id = rs.stop_id"; // Select end stop location

$filter_conditions = [];

// Filtering by user input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['user_name'])) {
        $user_name = $conn->real_escape_string($_POST['user_name']);
        $filter_conditions[] = "u.name LIKE '%$user_name%'";
    }
    if (!empty($_POST['payment_status'])) {
        $payment_status = $conn->real_escape_string($_POST['payment_status']);
        $filter_conditions[] = "b.payment_status = '$payment_status'";
    }
    if (!empty($_POST['booking_date'])) {
        $booking_date = $conn->real_escape_string($_POST['booking_date']);
        $filter_conditions[] = "DATE(b.booking_date) = '$booking_date'";
    }
    if (!empty($_POST['route_id'])) {
        $route_id = intval($_POST['route_id']);
        $filter_conditions[] = "r.route_id = $route_id";
    }
    if (!empty($_POST['stop_id'])) {
        $stop_id = intval($_POST['stop_id']);
        $filter_conditions[] = "b.start_stop_id = $stop_id"; // Change to start stop for filtering
    }
}

if (!empty($filter_conditions)) {
    $query .= ' WHERE ' . implode(' AND ', $filter_conditions);
}

$bookings = $conn->query($query);
$conn->close();
?>

<div class="content-wrapper">
    <div class="container mt-5">
        <h1 class="mb-4">Manage Seat Bookings</h1>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <input type="text" name="user_name" class="form-control" placeholder="User Name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <select name="payment_status" class="form-select">
                                <option value="">All Payment Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <input type="date" name="booking_date" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <select name="route_id" class="form-select">
                                <option value="">Select Route</option>
                                <?php while ($route = $routes->fetch_assoc()): ?>
                                    <option value="<?= $route['route_id'] ?>"><?= $route['route_name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <select name="stop_id" class="form-select">
                                <option value="">Select Start Stop</option>
                                <?php while ($stop = $stops->fetch_assoc()): ?>
                                    <option value="<?= $stop['stop_id'] ?>"><?= $stop['location'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Booking Table -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">All Bookings</h2>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>User Name</th>
                            <th>Seat Number</th>
                            <th>Route Name</th>
                            <th>Stop Location (End Stop)</th>
                            <th>Booking Date</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['booking_id']) ?></td>
                                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                <td><?= htmlspecialchars($booking['seat_number']) ?></td>
                                <td><?= htmlspecialchars($booking['route_name']) ?></td>
                                <td><?= htmlspecialchars($booking['stop_location']) ?></td>
                                <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                                <td><?= htmlspecialchars($booking['payment_status']) ?></td>
                                <td>
                                    <button onclick="cancelBooking(<?= $booking['booking_id'] ?>, '<?= htmlspecialchars($booking['user_email']) ?>')" class="btn btn-danger">Cancel</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    function cancelBooking(bookingId, userEmail) {
        if (confirm(`Are you sure you want to cancel this booking?`)) {
            fetch(`cancel_booking.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ booking_id: bookingId, user_email: userEmail })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message);
                }
            });
        }
    }
</script>

<?php include 'footer.php'; ?>









