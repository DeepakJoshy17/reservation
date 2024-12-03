<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialize $user_data to an empty array
$user_data = [];

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

if ($is_logged_in) {
    // User is logged in, fetch user data
    $user_id = $_SESSION['user_id'];

    // Include your database connection file
    include_once "db_connection.php"; // Ensure this file is present and contains a valid connection

    // Prepare and execute query to fetch user data
    $sql_user = "SELECT * FROM Users WHERE user_id = ? AND role = 'User'";
    $stmt_user = $conn->prepare($sql_user);

    if ($stmt_user === false) {
        // Log or display the error
        die("Error preparing statement: " . $conn->error);
    }

    $stmt_user->bind_param("i", $user_id);

    if (!$stmt_user->execute()) {
        // Log or display the error
        die("Error executing query: " . $stmt_user->error);
    }

    $result_user = $stmt_user->get_result();

    // Check if user data exists
    if ($result_user->num_rows > 0) {
        // User data found, fetch and assign to $user_data
        $user_data = $result_user->fetch_assoc();
    }

    $stmt_user->close();
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
  <title>Home Page</title>
  <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
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
          <li class="nav-item">
            <a class="nav-link tm-nav-link" href="userhome.php">Home</a>
          </li>
         <!-- <li class="nav-item">
            <a class="nav-link tm-nav-link" href="seatview.php">Book</a>
          </li>-->
          <?php if ($is_logged_in): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-user-circle"></i> <?= htmlspecialchars($user_data['name'] ?? 'User') ?>
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


