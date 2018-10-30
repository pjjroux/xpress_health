<?php 
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/controller/order_controller.php');

  if (isset($_SESSION['cart'])) {
    $checkout_count = 0;
    foreach ($_SESSION['cart'] as $cart_item) {
      $checkout_count +=  $cart_item['qty'];
    }
  }  

  $action = (isset($_GET['action'])) ? $_GET['action'] : null ;
  switch ($action) {
    case 'orders':
      $order_history = getOrdersByID($_SESSION['client_id']);
      break;
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Xpress Health - Account Screen">
    <meta name="author" content="Jaco Roux (5376-553-2)">
    <link rel="icon" href="assets/img/favicon.ico">

    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet">
    <link href="assets/libraries/noty/lib/noty.css" rel="stylesheet">
    <link rel="stylesheet"  href="assets/libraries/noty/lib/themes/bootstrap-v4.css" />
    <link rel="stylesheet" type="text/css" href="assets/libraries/DataTables/datatables.min.css"/>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/account.css">


    <title>Xpress Health - Account</title>

    <script>
      var action = '<?php echo $action ?>';
    </script>
  </head>
  <body>
 

   <section>
      <div id="logo">
        <img src="assets/img/logo.png" alt="Xpress Health Logo">
      </div>
    </section>

    <div align="center" style="margin-bottom:2%;">
        <div class="btn-group" role="group" aria-label="Select a report">
            <a href="account.php?action=edit" class="btn btn-success">Edit Account Detail</a>
            <a href="account.php?action=orders" class="btn btn-success">Order History</a>
            <a href="account.php?action=change_password" class="btn btn-success">Change Password</a>
        </div>
    </div>

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
        </ul>
      </div>
    </nav>
    

    <?php if ($action == 'edit') : ?>
    <h4 class="table-header">Edit Account Detail</h4>
    <section id="main">
      <div class="container-fluid" id="table-area">
        <form class="form-account" id="registration_form" method="post" action="controller/client_controller.php?action=updateClient" onsubmit="return validateForm()" >
          <h1 class="h3 mb-3 font-weight-normal">My account</h1>

          <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
              <label for="inputEmail" class="sr-only">Email address</label>
              <input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="Email address" required autofocus>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
              <label for="inputID" class="sr-only">ID number</label>
              <input type="text" id="inputID" name="inputID" class="form-control" placeholder="ID number" required>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
              <label for="inputName" class="sr-only">Name</label>
              <input type="text" id="inputName" name="inputName" class="form-control" placeholder="Name" required>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
              <label for="inputSurname" class="sr-only">Surname</label>
              <input type="text" id="inputSurname" name="inputSurname" class="form-control" placeholder="Surname" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-12">
              <label for="inputAddress" class="sr-only">Address</label>
              <input type="text" id="inputAddress" name="inputAddress" class="form-control" placeholder="Address" required>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <label for="inputPostal" class="sr-only">Postal code</label>
              <input type="text" id="inputPostal" name="inputPostal" class="form-control" placeholder="Postal code" required>
            </div>
          </div>
        
          <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
              <label for="inputTelHome" class="sr-only">Tel (Home)</label>
              <input type="text" id="inputTelHome" name="inputTelHome" class="form-control" placeholder="Tel (Home)">
            </div>
            <div class="col-12 col-md-6 col-lg-6">
              <label for="inputTelWork" class="sr-only">Tel (Work)</label>
              <input type="text" id="inputTelWork" name="inputTelWork" class="form-control" placeholder="Tel (Work)">
            </div>
          </div>
          
          <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
              <label for="inputCell" class="sr-only">Cell</label>
              <input type="text" id="inputCell" name="inputCell" class="form-control" placeholder="Cell">
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
              <select class="form-control" id="inputRef" name="inputRef" required>
                <option value="" disabled selected>Reference</option>
              </select>
            </div>
          </div>

          <div align="right"><button class="btn btn-success" type="submit"><i class="fa fa-pencil-square-o"></i> Update</button></div>
        </form>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($action == 'orders') : ?>
      <h4 class="table-header">Order History</h4>
      <section id="main">
        <div class="container-fluid" id="table-area">
          <?php if (!empty($order_history)) { ?>
          <table id="orders" class="table table-sm table-bordered table-hover" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th scope="col">Invoice #</th>
                <th scope="col">Date</th>
                <th scope="col">Customer ID</th>
                <th scope="col">Total</th>
                <th scope="col">Email</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order_history as $order) : ?>
                <tr>
                  <th scope="row"><?php echo $order['inv_num'] ?></th>
                  <td><?php echo $order['inv_date'] ?></td>
                  <td><?php echo $order['client_id'] ?></td>
                  <td align="right"><?php echo 'R' . number_format($order['grand_total'], 2, '.', ' ') ?></td>
                  <td align="center">
                    <button class="btn btn-success" onclick="email_invoice('<?php echo $order['inv_num'] ?>')" title="Email invoice"><i class="fa fa-envelope"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php } else { ?>
          <h4 class="no_orders">No order history</h4>
          <?php }  ?>
        </div>
      </section>
    <?php endif; ?> 

    <?php if ($action == 'change_password') : ?>
      <h4 class="table-header">Change Password</h4>
      <section id="main">
        <div class="container-fluid" id="table-area">
          <form class="form-account" id="password_form" method="post" action="controller/client_controller.php?action=updatePassword" onsubmit="return validateFormPassword()" >
            <h1 class="h3 mb-3 font-weight-normal">Change Password</h1>

            <div class="row">
              <div class="col-12 col-md-6 col-lg-6">
                <label for="inputPassword" class="sr-only">Current Password</label>
                <input type="password" id="inputCurrentPassword" name="inputCurrentPassword" class="form-control" placeholder="Current Password" required>    
              </div>
            </div>
            
            <div class="row">
              <div class="col-12 col-md-6 col-lg-6">
                <label for="inputPassword" class="sr-only">New Password</label>
                <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="New Password" required>    
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-6 col-lg-6">
                <label for="inputRePassword" class="sr-only">Confirm New Password</label>
                <input type="password" id="inputRePassword" name="inputRePassword" class="form-control" placeholder="Confirm New Password" required>
              </div>
            </div>
            

            <div align="right"><button class="btn btn-success" type="submit"><i class="fa fa-pencil-square-o"></i> Update Password</button></div>
          </form>
        </div>
      </section>
    <?php endif; ?>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    <script src="assets/libraries/noty/lib/noty.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/libraries/DataTables/datatables.min.js"></script>
    <script src="assets/js/jquery.inputmask.bundle.js"></script>
    <script src="assets/js/account.js"></script>
  </body>
</html>







