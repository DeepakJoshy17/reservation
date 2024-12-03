<?php
// Include database connection
include_once "db_connection.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Infinite Loop - Bootstrap 4.0 HTML Template</title>
    <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
    <link rel="stylesheet" href="slick/slick.css">
    <link rel="stylesheet" href="slick/slick-theme.css">
    <link rel="stylesheet" href="magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
    <!-- Add additional custom styles here if needed -->
</head>

<body>

    <?php include "includes/headeradminparallax.php"; ?>


    <section id="testimonials" class="tm-section-pad-top tm-parallax-2">
        <div class="container tm-testimonials-content">
            <div class="row">
                <div class="col-lg-12 tm-content-box">
                    <h2 class="text-white text-center mb-4 tm-section-title">What Our Users Say</h2>
                    <p class="mx-auto tm-section-desc text-center">
                    Real experiences from our satisfied passengers who’ve enjoyed unforgettable journeys with Waterway.
                    </p>
                    <div class="mx-auto tm-gallery-container tm-gallery-container-2">
                        <div class="tm-testimonials-carousel">
                            <figure class="tm-testimonial-item">
                                <img src="img/t1.jpg" alt="Image" class="img-fluid mx-auto">
                                <blockquote>Waterway made booking a boat so easy and hassle-free. I highly recommend it to anyone looking for a great boating experience!</blockquote>
                                <figcaption>Deepak (Kottayam)</figcaption>
                            </figure>

                            <figure class="tm-testimonial-item">
                                <img src="img/t2.jpg" alt="Image" class="img-fluid mx-auto">
                                <blockquote>Waterway provided me with an unforgettable boating experience. Their service is top-notch and the staff are very friendly and helpful!</blockquote>
                                <figcaption>Ben (Ernakulam)</figcaption>
                            </figure>

                            <figure class="tm-testimonial-item">
                                <img src="img/t3.jpg" alt="Image" class="img-fluid mx-auto">
                                <blockquote>Waterway provided me with an unforgettable boating experience. Their service is top-notch and the staff are very friendly and helpful!</blockquote>
                                <figcaption>Mariya (Kottayam)</figcaption>
                            </figure>

                            <figure class="tm-testimonial-item">
                                <img src="img/t1.jpg" alt="Image" class="img-fluid mx-auto">
                                <blockquote>Booking with Waterway was a breeze. The entire process was smooth and straightforward, and the boat ride itself was simply amazing.</blockquote>
                                <figcaption>Amal (Ernakulam)</figcaption>
                            </figure>

                            <figure class="tm-testimonial-item">
                                <img src="img/t2.jpg" alt="Image" class="img-fluid mx-auto">
                                <blockquote>Waterway exceeded my expectations in every way and made sure we had everything we needed. Highly recommend!.</blockquote>
                                <figcaption>Noyal (Kottayam)</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tm-bg-overlay"></div>
    </section>

    <section id="gallery" class="tm-section-pad-top">
        <div class="container tm-container-gallery">
            <div class="row">
                <div class="text-center col-12">
                    <h2 class="tm-text-primary tm-section-title mb-4">Gallery</h2>
                    <p class="mx-auto tm-section-desc">
                        Praesent sed pharetra lorem, blandit convallis mi. Aenean ornare elit ac metus lacinia, sed iaculis nibh semper. Pellentesque est urna, lobortis eu arcu a, aliquet tristique urna.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="mx-auto tm-gallery-container">
                        <div class="grid tm-gallery">
                            <a href="img/g1.jpeg">
                                <figure class="effect-honey tm-gallery-item">
                                    <img src="img/g1m.jpeg" alt="Image 1" class="img-fluid">
                                    <figcaption>
                                        <h2><i>Travel Through <span>Kerala!</span></i></h2>
                                    </figcaption>
                                </figure>
                            </a>
                            <a href="img/g12.jpg">
                                <figure class="effect-honey tm-gallery-item">
                                    <img src="img/g12.jpg" alt="Image 2" class="img-fluid">
                                    <figcaption>
                                        <h2><i>Experience <span>The nights</span></i></h2>
                                    </figcaption>
                                </figure>
                            </a>
                            <a href="img/gallery-img-03.jpg">
                                <figure class="effect-honey tm-gallery-item">
                                    <img src="img/gallery-tn-03.jpg" alt="Image 3" class="img-fluid">
                                    <figcaption>
                                        <h2><i>Explore Through <span>Cities</span></i></h2>
                                    </figcaption>
                                </figure>
                            </a>
                            <a href="img/g11.jpg">
                                <figure class="effect-honey tm-gallery-item">
                                    <img src="img/g11.jpg" alt="Image 4" class="img-fluid">
                                    <figcaption>
                                        <h2><i>Waterway <span>Boats</span></i></h2>
                                    </figcaption>
                                </figure>
                            </a>
                            <a href="img/g2.jpg">
                                <figure class="effect-honey tm-gallery-item">
                                    <img src="img/g2m.jpg" alt="Image 5" class="img-fluid">
                                    <figcaption>
                                        <h2><i>Wellness <span>Physical</span></i></h2>
                                    </figcaption>
                                </figure>
                            </a>
                            <a href="img/g2.jpg">
                                <figure class="effect-honey tm-gallery-item">
                                    <img src="img/g2m.jpg" alt="Image 6" class="img-fluid">
                                    <figcaption>
                                        <h2><i>Spend Time <br> Through<span> Lake</span></i></h2>
                                    </figcaption>
                                </figure>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include "includes/footercontact.php"; ?>
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
