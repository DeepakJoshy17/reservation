<?php
session_start();
include 'headeradmin.php'; 
include 'sidebar.php';
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

// Fetch pending and paid bookings
$query = "SELECT b.booking_id, b.booking_date, b.payment_status, u.name AS user_name, s.seat_number, r.route_name 
          FROM Seat_Bookings b
          JOIN Users u ON b.user_id = u.user_id
          JOIN Seats s ON b.seat_id = s.seat_id
          JOIN Schedules sch ON b.schedule_id = sch.schedule_id
          JOIN Routes r ON sch.route_id = r.route_id
          WHERE b.payment_status IN ('Pending', 'Paid')";

$bookings = $conn->query($query);
$conn->close();
?>

<div class="content-wrapper">
    <div class="container mt-5">
        <h1 class="mb-4">Manage Seat Bookings</h1>
        
        <!-- Booking Table -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Pending and Paid Bookings</h2>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>User Name</th>
                            <th>Seat Number</th>
                            <th>Route Name</th>
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
                                <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                                <td><?= htmlspecialchars($booking['payment_status']) ?></td>
                                <td>
                                    <button onclick="editBooking(<?= $booking['booking_id'] ?>)" class="btn btn-warning">Edit</button>
                                    <button onclick="cancelBooking(<?= $booking['booking_id'] ?>, '<?= htmlspecialchars($booking['user_name']) ?>')" class="btn btn-danger">Cancel</button>
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
    function cancelBooking(bookingId, userName) {
        if (confirm(`Are you sure you want to cancel this booking for ${userName}?`)) {
            fetch(`cancel_booking.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ booking_id: bookingId })
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
