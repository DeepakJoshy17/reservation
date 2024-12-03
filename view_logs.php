<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

include 'db_connection.php';

function fetch_logs() {
    global $conn;
    $query = "SELECT * FROM Admin_Logs ORDER BY timestamp DESC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>ID</th><th>Admin ID</th><th>Action</th><th>Log Time</th><th>Description</th></tr></thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['log_id']}</td><td>{$row['admin_id']}</td><td>{$row['action']}</td><td>{$row['log_time']}</td><td>{$row['description']}</td></tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-info'>No logs found.</div>";
    }
  //  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
</head>

<body>
    
    <?php include 'includes/headeradmin.php'; ?>
    <br><br><br>

    <div class="container mt-5">
        <h1 class="mb-4">Admin Logs</h1>
        <?php fetch_logs(); ?>
    </div>
    <br><br><br>
    <?php include "includes/footer.php"; ?>
  
<script src="js/jquery-1.9.1.min.js"></script>     
    <script src="slick/slick.min.js"></script>
    <script src="magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="js/easing.min.js"></script>
    <script src="js/jquery.singlePageNav.min.js"></script>     
    <script src="js/bootstrap.min.js"></script> 
    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

