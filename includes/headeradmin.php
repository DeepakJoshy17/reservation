<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize $admin_data to an empty array
$admin_data = [];

// Check if admin is logged in
$is_logged_in = isset($_SESSION['admin_id']);

if ($is_logged_in) {
    // Admin is logged in, fetch admin data
    $admin_id = $_SESSION['admin_id'];
    
    // Include your database connection file
    include_once "db_connection.php"; // Ensure this file is present and contains a valid connection

    // Prepare and execute query to fetch admin data
    $sql_admin = "SELECT * FROM Users WHERE user_id = ? AND role = 'Admin'";
    $stmt_admin = $conn->prepare($sql_admin);
    
    if ($stmt_admin === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt_admin->bind_param("i", $admin_id);

    if (!$stmt_admin->execute()) {
        die("Error executing query: " . $stmt_admin->error);
    }

    $result_admin = $stmt_admin->get_result();

    // Check if admin data exists
    if ($result_admin->num_rows > 0) {
        $admin_data = $result_admin->fetch_assoc();
    }

    $stmt_admin->close();
} else {
    // Admin not logged in, redirect to login page
    header("Location: loginadminhtml.php");
    exit();
}

function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

$current_page = getCurrentPage();
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
  <style>
  
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-md tm-navbar scroll" id="tmNav">
    <div class="container">
      <div class="tm-next">
        <a href="#infinite" class="navbar-brand">Waterway</a>
      </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars navbar-toggler-icon"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
       
        <?php if ($is_logged_in): ?>
               <!-- Admin Navigation -->
               <li class="nav-item">
              <a class="nav-link tm-nav-link" href="admindashboard.php">Home</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="adminMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Others
              </a>
              <div class="dropdown-menu" aria-labelledby="adminMenu">
                <a href="admindashboard.php" class="dropdown-item">Dashboard</a>
                <a href="view_logs.php" class="dropdown-item">Logs</a>
                <div class="dropdown-divider"></div>
                <a href="manage_boats.php" class="dropdown-item">Boats</a>
                <a href="manage_routes.php" class="dropdown-item">Routes</a>
                <a href="manage_stops.php" class="dropdown-item">Stops</a>
                <a href="manage_stop_pricing.php" class="dropdown-item">Prices</a>
                <a href="manage_schedules.php" class="dropdown-item">Schedules</a>
                <a href="admin_seat_management.php" class="dropdown-item">Seats</a>
              </div>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="adminMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle"></i> <?= $admin_data['name'] ?? 'Admin' ?>
              </a>
              <div class="dropdown-menu" aria-labelledby="userMenu">
                <a class="dropdown-item" href="profile.php">Profile</a>
                <a class="dropdown-item" href="logout_user.php">Logout</a>
              </div>
            </li>
            <?php else: ?>
            <li class="nav-item">
              <a class="nav-link tm-nav-link" href="loginuserhtml.php"><i class="fas fa-user-circle"></i> Login</a>
            </li>
            <?php endif; ?>
          </ul>
        </div>        
      </div>
    </nav>

  <script src="js/jquery-1.9.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>

