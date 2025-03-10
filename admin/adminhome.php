<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get total users
$result = $conn->query("SELECT COUNT(*) AS total_users FROM Users");
$total_users = $result->fetch_assoc()['total_users'];

// Query to get total boats
$result = $conn->query("SELECT COUNT(*) AS total_boats FROM Boats");
$total_boats = $result->fetch_assoc()['total_boats'];

// Initialize date range variables
$start_date = "";
$end_date = "";
$total_bookings_range = 0;
$total_amount_range = 0;

// Handle date range submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_date'], $_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate and sanitize input dates
    if (DateTime::createFromFormat('Y-m-d', $start_date) && DateTime::createFromFormat('Y-m-d', $end_date)) {
        // Query to get bookings and total amount within the date range
        $stmt = $conn->prepare("
            SELECT COUNT(t.ticket_id) AS total_bookings, SUM(t.amount) AS total_amount
            FROM Tickets t
            JOIN Seat_Bookings sb ON t.booking_id = sb.booking_id
            WHERE sb.booking_date BETWEEN ? AND ?
        ");
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        $total_bookings_range = $data['total_bookings'] ?: 0; // Default to 0 if NULL
        $total_amount_range = $data['total_amount'] ?: 0; // Default to 0 if NULL

        // Close the statement
        $stmt->close();
    }
}

// Query to get total bookings and total amount without date filter for initial load
$result = $conn->query("SELECT COUNT(*) AS total_bookings FROM Seat_Bookings");
$total_bookings = $result->fetch_assoc()['total_bookings'];

$result = $conn->query("SELECT SUM(amount) AS total_amount FROM Tickets");
$total_amount = $result->fetch_assoc()['total_amount'];

// Close the connection
$conn->close();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $total_users; ?></h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person"></i>
                        </div>
                        <a href="#" class="small-box-footer">Since 2024</a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $total_boats; ?></h3>
                            <p>Total Boats</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ship"></i>
                        </div>
                        <a href="#" class="small-box-footer">Since 2024</a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo ($start_date && $end_date) ? $total_bookings_range : $total_bookings; ?></h3>
                            <p>Total Bookings</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chair"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            <?php echo ($start_date && $end_date) ? "From $start_date to $end_date" : "Since 2024"; ?>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3><?php echo ($start_date && $end_date) ? $total_amount_range : $total_amount; ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            <?php echo ($start_date && $end_date) ? "From $start_date to $end_date" : "Since 2024"; ?>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- Date Range Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Update Bookings and Amount</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h4>Results for Selected Dates:</h4>
                            <p>Total Bookings: <?php echo $total_bookings_range; ?></p>
                            <p>Total Amount: ₹<?php echo $total_amount_range; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


