<?php
// userhome.php (or your main PHP file)

session_start();

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
  <title>Home Page </title>
  <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
  <link rel="stylesheet" href="slick/slick.css">
  <link rel="stylesheet" href="slick/slick-theme.css">
  <link rel="stylesheet" href="magnific-popup/magnific-popup.css">
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
  <style>
    /* Your existing styles */
    /* ... */

    /* Additional styles for the search results */
    /* Transparent button styles */
    .navbar .dropdown-toggle {
        background: transparent; /* Make the background transparent */
        border: none; /* Remove any border */
        color: #3377AA; /* Set text color to white (or match your background) */
        padding: 0; /* Adjust padding if necessary */
        font-size: 1em; /* Ensure the font size is appropriate */
    }

    .navbar .dropdown-toggle:focus {
        outline: none; /* Remove focus outline */
    }
    .book-link{
      color:white;
    }
    #search-results {
        margin-top: 20px;
    }
  </style>
</head>
<body>
  <!-- Hero section -->
  <section id="infinite" class="text-white tm-font-big tm-parallax">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-md tm-navbar" id="tmNav">              
      <div class="container">   
        <div class="tm-next">
          <a href="#infinite" class="navbar-brand">Waterway</a>
        </div>             
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="fas fa-bars navbar-toggler-icon"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link tm-nav-link" href="#infinite">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link tm-nav-link" href="#search">Book</a>
            </li>
            <li class="nav-item">
              <a class="nav-link tm-nav-link" href="#whatwedo">What We Do</a>
            </li>
            <li class="nav-item">
              <a class="nav-link tm-nav-link" href="#testimonials">Testimonials</a>
            </li>
            <li class="nav-item">
              <a class="nav-link tm-nav-link" href="#gallery">Gallery</a>
            </li>
            <li class="nav-item">
              <a class="nav-link tm-nav-link" href="#contact">Contact</a>
            </li>
            <?php if ($is_logged_in): ?>
              <li class="nav-item dropdown">
                    <a class="nav-link tm-nav-link " id="userMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($user_data['name'] ?? 'User') ?> <i class="fas fa-caret-down"></i>
            </a>
                    <div class="dropdown-menu" aria-labelledby="userMenu">
                        <a class="dropdown-item" href="profile.php">Profile</a>
                        <a class="dropdown-item" href="logout_user.php">Logout</a>
                    </div>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link tm-nav-link" href="loginuserhtml.php">
                        <i class="fas fa-user-circle"></i> Login
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>        
    </div>
</nav>
    
    <div class="text-center tm-hero-text-container">
      <div class="tm-hero-text-container-inner">
        <h2 class="tm-hero-title">Waterway</h2>
        <p class="tm-hero-subtitle">
            Reservation Website
            <br>
            <!-- Button Section -->
            <a id="book" href="#search" class="book-link">Book Seats</a>
        </p>
      </div>        
    </div>
    
        
  </section>
