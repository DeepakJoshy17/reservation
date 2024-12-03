<?php include 'headeradmin.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
include 'db_connection.php';

// Function to fetch logs based on a date range
function fetch_logs_by_date_range($start_date, $end_date) {
    global $conn;

    // Prepare the SQL query to fetch logs for the specified date range
    $query = "SELECT * FROM Admin_Logs WHERE DATE(timestamp) BETWEEN ? AND ? ORDER BY timestamp DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>Log ID</th><th>Admin ID</th><th>Action</th><th>Description</th><th>Timestamp</th></tr></thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['log_id']}</td>
                <td>{$row['admin_id']}</td>
                <td>{$row['action']}</td>
                <td>{$row['description']}</td>
                <td>{$row['timestamp']}</td>
            </tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-info'>No logs found for this date range.</div>";
    }

    $stmt->close();
}

// Get today's date
$today = date("Y-m-d");

// Handle form submission
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $today;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $today;
?>

<style>
    .table {
        margin-top: 20px;
        width: 100%;
        border-collapse: collapse;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .table th, .table td {
        text-align: center;
        padding: 12px;
        border: 1px solid #dee2e6;
    }

    .table thead th {
        background-color: #f8f9fa; /* Light grey color for the header */
        color: #333; /* Darker text for contrast */
    }

    .alert {
        margin-top: 20px;
        text-align: center;
    }
</style>

<div class="content-wrapper">
    <div class="container mt-5">
        <h1 class="mb-4">Admin Logs</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>" max="<?php echo $today; ?>">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>" max="<?php echo $today; ?>">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Search</button>
        </form>
        
        <!-- Fetch logs only after the form submission -->
        <?php fetch_logs_by_date_range($start_date, $end_date); ?>
    </div>
</div>

<?php include 'footer.php'; ?>


