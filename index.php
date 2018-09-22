<?php
session_start();

require_once('controller/product_controller.php');

if (isset($_SESSION['cart'])) {
  $checkout_count = 0;
  foreach ($_SESSION['cart'] as $cart_item) {
    $checkout_count +=  $cart_item['qty'];
  }
}

// Get the top 4 best selling products
$best_sellers = getBestSellingProducts();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Xpress Health">
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

    <title>Xpress Health</title>
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
            <a class="nav-link" href="contact.html"><i class="fa fa-info-circle"></i> Contact Us</a>
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
              <a class="nav-link" href="account.html"><i class="fa fa-user"></i> <?php echo $_SESSION['client_name'] ?></a>
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
      </div>
      <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="search" placeholder="Search..." aria-label="Search">
        <button class="btn btn-success my-2 my-sm-0" type="submit">Search</button>
      </form>
    </nav>
    
    <section>
      <div id="logo">
        <img src="assets/img/logo.png" alt="Xpress Health Logo">
      </div>
    </section>

    <section id="content">
      <div class="container-fluid">
        <div id="product-area">
          <div class="row">
            <div class="col-12">
              <h2 class="shop-header">Best Sellers</h2>
            </div>
          </div>
          
          
          <div class="row">
            <?php foreach ($best_sellers as $value) : ?>
              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <div class="product card" align="center"> 
                  <div class="product-image">
                    <img class="card-img-top" src="<?php echo $value['img'] ?>" alt="Product image">
                  </div>

                  <h5 class="card-title"><?php echo $value['supplement_id'] ?></h5>
                  <h6><?php echo $value['description'] ?></h6>
                  <p class="card-text"><?php echo $value['long_description'] ?></p>

                  <div class="<?php echo $value['stock_style'] ?>">
                    <p><?php echo $value['stock_status'] ?></p>
                  </div>

                  <div class="price">
                    <p><?php echo $value['cost'] ?></p>
                  </div>

                  <div class="text-right">
                    <?php if ($value['stock_status'] != 'Out of stock') { ?>
                      <a href="#" onclick="buy('<?php echo $value['supplement_id'] ?>');return false;" class="btn btn-success" title="Buy"><i class="fa fa-shopping-cart"></i> Add to cart</a>
                    <?php } else { ?>
                      <a href="#" disabled class="btn btn-success" title="Buy"><i class="fa fa-shopping-cart"></i> Add to cart</a>
                    <?php } ?>
                  </div>   

                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <footer>
      <div class="container text-center">
        <div class="row">
          <div class="col-12">
            <p>XH Wellness Center &copy; 2018</p>
          </div>     
        </div>
      </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    <script src="assets/libraries/noty/lib/noty.js" type="text/javascript"></script>
    <script src="assets/js/login.js"></script>
    <script src="assets/js/cart.js"></script>

  </body>
</html>