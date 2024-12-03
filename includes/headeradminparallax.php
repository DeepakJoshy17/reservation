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
  <title>Home Page </title>
  <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
  <link rel="stylesheet" href="slick/slick.css">
  <link rel="stylesheet" href="slick/slick-theme.css">
  <link rel="stylesheet" href="magnific-popup/magnific-popup.css">
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
  <style>
/* Style for the link with ID 'book' */
/* Style for the link with ID 'book' */
/* Hero Section Title */
.tm-hero-title {
    color: #fff !important; /* Make sure the title is white */
    font-size: 3rem; /* Adjust font size as needed */
    margin-bottom: 20px;
}

/* Hero Section Subtitle */
.tm-hero-subtitle {
  color: #fff !important; /* Make sure the title is white */
    color: rgba(255, 255, 255, 0.9); /* Ensure subtitle is also white with transparency */
}

/* Book Now Button Styling */
.book-link {
    color: white; /* Dark text color */
    /*background-color: #fff; *//* White background */
    padding: 10px 20px; /* Padding for better button size */
   /* border: 2px solid #333; *//* Add a border */
    border-radius: 5px; /* Rounded corners */
    text-decoration: none; /* Remove underline */
    transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
}

.book-link:hover {
    background-color: #333; /* Dark background on hover */
    color: #fff; /* White text on hover */
    text-decoration: none; /* Remove underline */
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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="fas fa-bars navbar-toggler-icon"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto">
        
            
            <?php if ($is_logged_in): ?>
               <!-- Admin Navigation -->
               <li class="nav-item">
              <a class="nav-link tm-nav-link" href="#infinite">Home</a>
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
    
    <div class="text-center tm-hero-text-container">
  <div class="tm-hero-text-container-inner">
  <h2 class="tm-hero-title">Waterway</h2>
<p class="tm-hero-subtitle">
    Administer Boats Routes Seats
    <br>
    <!-- Button Section -->
    <a id="book" href="manage_schedules.php" class="book-link">Manage Schedules</a>
</p>

  </div>        
</div>


    <div class="tm-next tm-intro-next">
     <!-- <a href="#whatwedo" class="text-center tm-down-arrow-link">
        <i class="fas fa-2x fa-arrow-down tm-down-arrow"></i>
      </a>-->
    </div>      
  </section>


    
  <script src="js/jquery-1.9.1.min.js"></script>     
    <script src="slick/slick.min.js"></script>
    <script src="magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="js/easing.min.js"></script>
    <script src="js/jquery.singlePageNav.min.js"></script>     
    <script src="js/bootstrap.min.js"></script> 
    <script>

     function getOffSet(){
        var _offset = 450;
        var windowHeight = window.innerHeight;

        if(windowHeight > 500) {
          _offset = 400;
        } 
        if(windowHeight > 680) {
          _offset = 300
        }
        if(windowHeight > 830) {
          _offset = 210;
        }

        return _offset;
      }

      function setParallaxPosition($doc, multiplier, $object){
        var offset = getOffSet();
        var from_top = $doc.scrollTop(),
          bg_css = 'center ' +(multiplier * from_top - offset) + 'px';
        $object.css({"background-position" : bg_css });
      }

      // Parallax function
      // Adapted based on https://codepen.io/roborich/pen/wpAsm        
      var background_image_parallax = function($object, multiplier, forceSet){
        multiplier = typeof multiplier !== 'undefined' ? multiplier : 0.5;
        multiplier = 1 - multiplier;
        var $doc = $(document);
        // $object.css({"background-attatchment" : "fixed"});

        if(forceSet) {
          setParallaxPosition($doc, multiplier, $object);
        } else {
          $(window).scroll(function(){          
            setParallaxPosition($doc, multiplier, $object);
          });
        }
      };

      var background_image_parallax_2 = function($object, multiplier){
        multiplier = typeof multiplier !== 'undefined' ? multiplier : 0.5;
        multiplier = 1 - multiplier;
        var $doc = $(document);
        $object.css({"background-attachment" : "fixed"});
        
        $(window).scroll(function(){
          if($(window).width() > 768) {
            var firstTop = $object.offset().top,
                pos = $(window).scrollTop(),
                yPos = Math.round((multiplier * (firstTop - pos)) - 186);              

            var bg_css = 'center ' + yPos + 'px';

            $object.css({"background-position" : bg_css });
          } else {
            $object.css({"background-position" : "center" });
          }
        });
      };
      
      $(function(){
        // Hero Section - Background Parallax
        background_image_parallax($(".tm-parallax"), 0.30, false);
        background_image_parallax_2($("#contact"), 0.80);   
        background_image_parallax_2($("#testimonials"), 0.80);   
        
        // Handle window resize
        window.addEventListener('resize', function(){
          background_image_parallax($(".tm-parallax"), 0.30, true);
        }, true);

        // Detect window scroll and update navbar
        $(window).scroll(function(e){          
          if($(document).scrollTop() > 120) {
            $('.tm-navbar').addClass("scroll");
          } else {
            $('.tm-navbar').removeClass("scroll");
          }
        });
        
        // Close mobile menu after click 
        $('#tmNav a').on('click', function(){
          $('.navbar-collapse').removeClass('show'); 
        })

      
        // Add smooth scrolling to all links
        // https://www.w3schools.com/howto/howto_css_smooth_scroll.asp
        $("a").on('click', function(event) {
          if (this.hash !== "") {
            event.preventDefault();
            var hash = this.hash;

            $('html, body').animate({
              scrollTop: $(hash).offset().top
            }, 600, 'easeInOutExpo', function(){
              window.location.hash = hash;
            });
          } // End if
        });

        // Pop up
        $('.tm-gallery').magnificPopup({
          delegate: 'a',
          type: 'image',
          gallery: { enabled: true }
        });

        $('.tm-testimonials-carousel').slick({
          dots: true,
          prevArrow: false,
          nextArrow: false,
          infinite: false,
          slidesToShow: 3,
          slidesToScroll: 1,
          responsive: [
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 2
              }
            }, 
            {
              breakpoint: 480,
              settings: {
                  slidesToShow: 1
              }                 
            }
          ]
        });

        // Gallery
        $('.tm-gallery').slick({
          dots: true,
          infinite: false,
          slidesToShow: 5,
          slidesToScroll: 2,
          responsive: [
          {
            breakpoint: 1199,
            settings: {
              slidesToShow: 4,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 767,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
        });
      });
      
    </script>

 
</body>
</html>