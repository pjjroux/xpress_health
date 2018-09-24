<?php
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/controller/product_controller.php');

// Get all products
$products = getProducts();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Xpress Health - Products">
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

    <title>Xpress Health - Products</title>
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
      </div>
    </nav>


    <div class="container-fluid">
      <div class="card shopping-cart">
        <div class="card-header bg-light text-dark">
          <i class="fa fa-list" aria-hidden="true"></i>
          Product listing
          <div class="clearfix"></div>
        </div>

        <div class="card-body">
          <?php 
            if (isset($products) && count($products) > 0) {
              foreach ($products as $product) :  
          ?>
                <!-- PRODUCT -->
                <div class="row">
                  <div class="col-12 col-sm-12 col-md-2 text-center">
                    <img class="img-responsive" src="<?php echo $product['img'] ?>" alt="prewiew" width="100" height="100">
                  </div>
                  <div class="col-12 text-sm-center col-sm-12 text-md-left col-md-6">
                    <h4 class="product-name"><strong><?php echo $product['supplement_id'] ?></strong></h4>
                    <h4>
                        <small><?php echo $product['long_description'] ?></small>
                    </h4>
                  </div>
                  <div class="col-12 col-sm-12 text-sm-center col-md-4 text-md-right row">
                    <div class="col-3 col-sm-3 col-md-6 text-md-right" style="padding-top: 5px">
                      <h6><strong><?php echo $product['cost'] ?> <span class="text-muted">x</span></strong></h6>
                    </div>
                    <div class="col-4 col-sm-4 col-md-4">
                      <div class="quantity">
                        <input type="button" value="+" class="plus" onclick="add_qty('<?php echo $product['supplement_id'] ?>')">
                        <input type="number" step="1" max="99" min="1" id="<?php echo $product['supplement_id'].'_qty' ?>" value="1" title="Qty" class="qty"
                              size="4" readonly>
                        <input type="button" value="-" class="minus" onclick="remove_qty('<?php echo $product['supplement_id'] ?>')">
                      </div>
                    </div>
                  </div>
                </div>
                
                <hr>
                <!-- END PRODUCT -->
          <?php
              endforeach;
          ?>
        </div>
        <?php } else { ?>
            <h4 class="empty_cart">No products found</h4>
        <?php } ?>
      </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    <script src="assets/libraries/noty/lib/noty.js" type="text/javascript"></script>
    <script src="assets/js/cart.js"></script>
  </body>
</html>