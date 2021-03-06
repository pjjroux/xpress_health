<?php
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/controller/cart_controller.php');

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
    <meta name="description" content="Xpress Health - Shopping Cart">
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
    <link rel="stylesheet" href="assets/css/cart.css">

    <title>Xpress Health - Shopping Cart</title>
  </head>
  <body>
    <div id="preloader"></div>
    
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

    <div class="container-fluid">
      <div class="card shopping-cart">
        <div class="card-header bg-light text-dark">
          <i class="fa fa-shopping-cart" aria-hidden="true"></i>
          Shopping Cart
          <a href="index.php" class="btn btn-outline-success btn-sm pull-right">Continue Shopping</a>
          <div class="clearfix"></div>
        </div>

        <div class="card-body">
          <?php 
            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
              $total_price = 0;
              foreach ($_SESSION['cart'] as $cart_item) :  
          ?>
                <!-- PRODUCT -->
                <div class="row">
                  <div class="col-12 col-sm-12 col-md-2 text-center">
                    <img class="img-responsive" src="<?php echo $cart_item['img'] ?>" alt="prewiew" width="100" height="100">
                  </div>
                  <div class="col-12 text-sm-center col-sm-12 text-md-left col-md-6">
                    <h4 class="product-name"><strong><?php echo $cart_item['supplement_id'] ?></strong></h4>
                    <h4>
                        <small><?php echo $cart_item['description'] ?></small>
                    </h4>
                  </div>
                  <div class="col-12 col-sm-12 text-sm-center col-md-4 text-md-right row">
                    <div class="col-6 col-sm-6 col-md-6 text-md-right" style="padding-top: 5px">
                      <h6><strong><?php echo 'R '.number_format($cart_item['cost_per_item'], 2, '.', ' ') ?> <span class="text-muted">x</span></strong></h6>
                    </div>
                    <div class="col-4 col-sm-4 col-md-4">
                      <div class="quantity">
                        <input type="button" value="+" class="plus" onclick="add_qty('<?php echo $cart_item['supplement_id'] ?>')">
                        <input type="number" step="1" max="99" min="1" id="<?php echo $cart_item['supplement_id'].'_qty' ?>" value="<?php echo $cart_item['qty'] ?>" title="Qty" class="qty"
                              size="4" readonly>
                        <input type="button" value="-" class="minus" onclick="remove_qty('<?php echo $cart_item['supplement_id'] ?>')">
                      </div>
                    </div>
                    <div class="col-2 col-sm-2 col-md-2 text-right">
                      <button type="button" class="btn btn-outline-danger btn-xs" onclick="remove_from_cart('<?php echo $cart_item['supplement_id'] ?>')">
                        <i class="fa fa-trash" aria-hidden="true" ></i>
                      </button>
                    </div>
                  </div>
                </div>
                
                <div align="right" class="sub_total">
                  <?php echo 'R '.number_format($cart_item['cost'], 2, '.', ' ') ?>
                </div>

                <hr>
                <!-- END PRODUCT -->
          <?php
              $total_price += $cart_item['cost'];
              endforeach;
          ?>
        </div>

        <div class="card-footer">
          <div class="pull-right" style="margin: 10px">
            <a href="#" class="btn btn-success pull-right" onclick="checkout()"><i class="fa fa-check"></i> Checkout</a>
            <div class="pull-right" style="margin: 5px">
                Total price: <b><?php echo 'R '.number_format($total_price, 2, '.', ' ') ?></b>
            </div>
          </div>
        </div>
        <?php } else { ?>
            <h4 class="empty_cart">Your shopping cart is empty.</h4>
        <?php } ?>
      </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="assets/js/load.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    <script src="assets/libraries/noty/lib/noty.js" type="text/javascript"></script>
    <script src="assets/js/login.js"></script>
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/search.js"></script>
  </body>
</html>