<?php
//session_start(); // Start the session

// Include database connection
include 'db_connection.php';

// Initialize variables for user data
$name = '';
$email = '';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Fetch user details from the database
    $user_id = $_SESSION['user_id'];
    $query = "SELECT name, email FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Contact Us</title>
  <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
  <link rel="stylesheet" href="slick/slick.css">
  <link rel="stylesheet" href="slick/slick-theme.css">
  <link rel="stylesheet" href="magnific-popup/magnific-popup.css">
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
</head>
<body>
<section id="contact" class="tm-section-pad-top tm-parallax-2">
    <div class="container tm-container-contact">
      <div class="row">
        <div class="text-center col-12">
            <h2 class="tm-section-title mb-4">Contact Us</h2>
            <p class="mb-5">If you have any concerns or complaints regarding our services, don't hesitate to contact us. Your satisfaction is our priority, and we strive to make things right.</p>
        </div>
        <div class="col-sm-12 col-md-6">
          <form id="enquiryForm">
            <input id="name" name="name" type="text" placeholder="Your Name" class="tm-input" value="<?= htmlspecialchars($name) ?>" required />
            <input id="email" name="email" type="email" placeholder="Your Email" class="tm-input" value="<?= htmlspecialchars($email) ?>" required />
            <textarea id="message" name="message" rows="8" placeholder="Message" class="tm-input" required></textarea>
            <button type="submit" class="btn tm-btn-submit">Submit</button>
          </form>
          <div id="responseMessage" class="mt-3"></div> <!-- To display messages -->
        </div>
        <div class="col-sm-12 col-md-6">
          <!-- Contact Details -->
          <div class="contact-item">
            <a rel="nofollow" href="https://www.tooplate.com/contact" class="item-link">
                <i class="far fa-2x fa-comment mr-4"></i>
                <span class="mb-0">Chat Online</span>
            </a>              
          </div>
          <div class="contact-item">
            <a rel="nofollow" href="mailto:mail@company.com" class="item-link">
                <i class="far fa-2x fa-envelope mr-4"></i>
                <span class="mb-0">waterway@gmail.com</span>
            </a>              
          </div>
          <div class="contact-item">
            <a rel="nofollow" href="https://www.google.com/maps" class="item-link">
                <i class="fas fa-2x fa-map-marker-alt mr-4"></i>
                <span class="mb-0">Kochi</span>
            </a>              
          </div>
          <div class="contact-item">
            <a rel="nofollow" href="tel:0100200340" class="item-link">
                <i class="fas fa-2x fa-phone-square mr-4"></i>
                <span class="mb-0">9497384572</span>
            </a>              
          </div>
        </div>
      </div><!-- row ending -->
    </div>
</section>

<script>
$(document).ready(function() {
    $('#enquiryForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        $.ajax({
            type: 'POST',
            url: 'admin/submit_enquiry.php', // URL to submit to
            data: $(this).serialize(), // Serialize form data
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#responseMessage').html('<div class="alert alert-success color=white">' + response.message + '</div>');
                    $('#enquiryForm')[0].reset(); // Reset the form after successful submission
                } else {
                    $('#responseMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#responseMessage').html('<div class="alert alert-danger">An error occurred while submitting your enquiry. Please try again.</div>');
            }
        });
    });
});
</script>

<script src="js/jquery-1.9.1.min.js"></script>     
<script src="slick/slick.min.js"></script>
<script src="magnific-popup/jquery.magnific-popup.min.js"></script>
<script src="js/easing.min.js"></script>
<script src="js/jquery.singlePageNav.min.js"></script>     
<script src="js/bootstrap.min.js"></script> 
</body>
</html>

