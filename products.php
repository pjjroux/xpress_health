<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/controller/product_controller.php');

if (isset($_SESSION['cart'])) {
  $checkout_count = 0;
  foreach ($_SESSION['cart'] as $cart_item) {
    $checkout_count +=  $cart_item['qty'];
  }
}

// Get all products
$limit = 10;  
$page = (isset($_GET["page"])) ? $_GET["page"] : 1 ;  
$start_from = ($page-1) * $limit;

$products = getProducts($start_from, $limit);

$products_total = getProductsTotal();


// Search data from search.js
$search_data = (isset($_GET['search_data'])) ? json_decode(base64_decode($_GET['search_data'])) : null ;
$search_data_url = (isset($_GET['search_data'])) ? $_GET['search_data'] : null ;

if (!is_null($search_data)) {
  $products = getProductsByIDs($search_data,$start_from, $limit);

  $products_total = count($search_data);
}

$total_pages = ceil($products_total / $limit);

if ($page == $total_pages) {
  $total_header = ($start_from + 1). ' - ' . $products_total . ' of ' .$products_total;
} else {
  $total_header = ($start_from + 1). ' - ' . ($start_from + 10) . ' of ' .$products_total;
}


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
    <link rel="stylesheet" href="assets/css/product.css">

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

    <div class="container-fluid">
      <div class="card shopping-cart">
        <div class="card-header bg-light text-dark">
        <div class="row">
          <div class="col-12">
          <nav class="pagination-bar">
          <ul class="pagination justify-content-end pagination-sm">

            <?php if ($page > 1) { ?>
              <li class="page-item">
                <a class="page-link" href="products.php?page=<?php echo $page - 1 ?>&search_data=<?php echo $search_data_url ?>" tabindex="-1">Previous</a>
              </li>
            <?php } else { ?>
              <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
              </li>
            <?php } ?>
            
            <?php for ($i=1; $i<=$total_pages; $i++) : ?> 
                <?php if ($i == $page) { ?>
                  <li class="page-item active"><a class="page-link" href="products.php?page=<?php echo $i ?>&search_data=<?php echo $search_data_url ?>"><?php echo $i ?></a></li> 
                <?php } else { ?>
                  <li class="page-item"><a class="page-link" href="products.php?page=<?php echo $i ?>&search_data=<?php echo $search_data_url ?>"><?php echo $i ?></a></li>
                <?php } ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages) { ?>
              <li class="page-item">
                <a class="page-link" href="products.php?page=<?php echo $page + 1 ?>&search_data=<?php echo $search_data_url ?>">Next</a>
              </li>
            <?php } else { ?>
              <li class="page-item disabled">
                <a class="page-link" href="#">Next</a>
              </li>
            <?php } ?>
          </ul>
        </nav>
          </div>
        </div>
        
          <i class="fa fa-list" aria-hidden="true"></i>
          Product listing
          <div class="clearfix"></div>

          <div class="product_counter" align="right">Products: <?php echo $total_header ?></div>
        </div>

        <div class="card-body">
          <?php 
            if (isset($products) && count($products) > 0) {
              foreach ($products as $product) :  
          ?>
                <!-- PRODUCT -->
                <div class="row">
                  <div class="col-12 col-sm-12 col-md-2 text-center">
                    <img class="img-responsive" src="<?php echo $product['img'] ?>" alt="prewiew" width="75" height="75">
                  </div>
                  <div class="col-12 text-sm-center col-sm-12 text-md-left col-md-6">
                    <h4 class="product-name"><strong><?php echo $product['supplement_id'] ?></strong></h4>
                    <h4 class="long_description">
                        <small><?php echo htmlspecialchars($product['long_description']) ?></small>
                    </h4>
                    <hr>
                    <p><?php echo 'Available: '.$product['stock_level'] ?></p>
                    <hr>
                  </div>
                  <div class="col-12 col-sm-12 text-sm-center col-md-4 text-md-right row price_row">
                    <div class="col-6 col-sm-6 col-md-6 text-md-right" style="padding-top: 5px">
                      <h6><strong><?php echo $product['cost'] ?> <span class="text-muted">x</span></strong></h6>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4">
                      <div class="quantity">
                        <input type="button" value="+" class="plus" onclick="add_qty('<?php echo $product['supplement_id'] ?>')">
                        <input type="number" step="1" max="99" min="1" id="<?php echo $product['supplement_id'].'_qty' ?>" value="1" title="Qty" class="qty"
                              size="4" readonly>
                        <input type="button" value="-" class="minus" onclick="remove_qty('<?php echo $product['supplement_id'] ?>')">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-6 col-sm-6 col-md-6 text-md-right">
                    <div class="<?php echo $product['stock_style'] ?>">
                      <p><?php echo $product['stock_status'] ?></p>
                    </div>
                  </div>
                  <div class="col-6 col-sm-6 col-md-6 text-right">
                    <?php if ($product['stock_status'] != 'Out of stock') { ?>
                      <a href="#" onclick="buy('<?php echo $product['supplement_id'] ?>');return false;" class="btn btn-success" title="Buy"><i class="fa fa-shopping-cart"></i> Add to cart</a>
                    <?php } else { ?>
                      <a href="#" disabled class="btn btn-success" title="Buy"><i class="fa fa-shopping-cart"></i> Add to cart</a>
                    <?php } ?>
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
    <script src="assets/libraries/responsive-paginate.js" type="text/javascript"></script>
    <script src="assets/js/product.js"></script>
    <script src="assets/js/search.js"></script>
  </body>
</html>