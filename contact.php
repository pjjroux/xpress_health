<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['cart'])) {
  $checkout_count = 0;
  foreach ($_SESSION['cart'] as $cart_item) {
    $checkout_count +=  $cart_item['qty'];
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Xpress Health - Contact Us">
    <meta name="author" content="Jaco Roux (5376-553-2)">
    <link rel="icon" href="assets/img/favicon.ico">

    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet">
    <link href="assets/libraries/noty/lib/noty.css" rel="stylesheet">
    <link rel="stylesheet"  href="assets/libraries/noty/lib/themes/bootstrap-v4.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/contact.css">

    <title>Xpress Health - Contact Us</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg fixed-top navbar-light">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="index.php"><i class="fa fa-home"></i> Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="products.php"><i class="fa fa-list"></i> Products</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.php"><i class="fa fa-info-circle"></i> Contact Us</a>
          </li>
        </ul>
        <ul class="navbar-nav mr-right mt-2 mt-lg-0"> 
          <?php if (!isset($_SESSION['client_name'])) { ?>  
            <li class="nav-item">
              <a class="nav-link" href="login.html"><i class="fa fa-sign-in"></i> Login</a>
            </li>   
            <li class="nav-item">
              <a class="nav-link" href="register.html"><i class="fa fa-user-plus"></i> Register</a>
            </li>
          <?php } else { ?> 
            <li class="nav-item">
              <a class="nav-link" href="account.php"><i class="fa fa-user"></i> <?php echo $_SESSION['client_name'] ?></a>
            </li>
            <li class="nav-item">
              <?php if (isset($checkout_count) && $checkout_count > 0) { ?>
                <a class="nav-link" href="cart.php"><i class="fa fa-shopping-cart"></i> Checkout <span class="badge badge-pill badge-success" style="margin-left:5px;"><?php echo $checkout_count ?></span></a>
              <?php } else { ?>
                <a class="nav-link" href="cart.php"><i class="fa fa-shopping-cart"></i> Checkout</a>
              <?php } ?>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" onclick="logout();return false;"><i class="fa fa-sign-out"></i> Logout</a>
            </li> 
          <?php } ?>   
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" id="search_box" name="search_box" type="search" placeholder="Search..." aria-label="Search">
          <button class="btn btn-success my-2 my-sm-0" id="btn_search" type="button">Search</button>
        </form>
      </div>
    </nav>
    
    <section>
      <div id="logo">
        <img id="logo_img" src="assets/img/logo.png" alt="Xpress Health Logo">
      </div>
    </section>

    <section id="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-sm-12 col-md-4 col-lg-4">
              <h2 class="shop-header">About Us</h2>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6 col-lg-6 about">
            <p>Traditional healthcare can be expensive and Xpress Health Wellness Center provides alternative healthcare options and supplements at an affordable price. 
              The business strives to improve the general health and lifestyle of persons with an active and demanding lifestyle. 
              We have been in business for over 10 years and our supplements enriches lives everyday.
            </p>

            <p>
              We stock a wide range of supplements and pride ourselves on quality, affordability and excellent customer service. 
              Please do not hesitate to contact us for any queries regarding any of our products and be sure to visit if you are in the area for a free health assessment.
            </p>

            <h2 class="shop-header">Contact Us</h2>

            <div class="contact-form">
              <form action="controller/contact_controller.php" method="post" onsubmit="loading();">
                  <div class="form-group">
                    <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
                  </div>
                  <div class="form-group">
                    <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control" id="contact" placeholder="Contact number" name="contact" required>
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" rows="5" id="message" placeholder="Message" name="message" required></textarea>
                  </div>
                  <div class="text-right">
                    <button type="submit" class="btn btn-primary" title="Send Message"><i class="fa fa-send"></i> Send</button>
                  </div>
                </form>
            </div>
          </div>
          
          <div class="col-12 col-md-6 col-lg-6">
            <img src="assets/img/store.jpg" alt="Store" class="store-img img-fluid img-thumbnail">

            <div class="contact-icons text-left">
              <div class="row">
                <div class="col-12">
                  <a href="tel:0186331531"><i class="fa fa-lg fa-phone"></i> 012 000 1111</a>
                </div>

                <div class="col-12">
                  <a href="mailto:sales@xpresshealth.co.za"><i class="fa fa-lg fa-envelope"></i> sales@xpresshealth.co.za</a>
                </div>

                <div class="col-12">
                  <a href="https://twitter.com/Twitter" target="_blank"><i class="fa fa-lg fa-twitter"></i> @XpressHealth</a>
                </div>
              </div>
            </div>
            
          </div>
        </div>

      </div>
    </section>

    <div id="map"></div>

    <footer>
      <div class="container text-center">
        <div class="row">
          <div class="col-12">
            <p>XH Wellness Center &copy; 2018</p>
          </div>     
        </div>
      </div>
    </footer>

    <script>
    // Initialize and add the map
    function initMap() {
      // The location of XpressHealth
      var address = {lat: -25.767658, lng: 28.199264};
      var map = new google.maps.Map(
        document.getElementById('map'), {zoom: 15, center: address});
        var marker = new google.maps.Marker({position: address, map: map});
      }
    </script>

    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDApDsTpy3vLGiNDEPL40T1hCzCRrjOjKg&callback=initMap">
    </script>


    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    <script src="assets/libraries/noty/lib/noty.js" type="text/javascript"></script>
    <script src="assets/js/login.js"></script>
    <script src="assets/js/contact.js"></script>
    <script src="assets/js/search.js"></script>
  </body>
</html>