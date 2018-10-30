<?php
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/controller/client_controller.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/controller/order_controller.php');

// If admin user is not logged in redirect to index
if (!isset($_SESSION['client_name'])) { 
  header('Location: index.php');
}
// Get outstanding orders
$orders = getOrders();
$order_count = (!empty($orders)) ? count($orders) : 0 ;

// Get all clients
$clients = getClients();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Xpress Health - Clients">
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
    <link rel="stylesheet" href="assets/css/admin.css">

    <title>Xpress Health - Clients</title>
  </head>
  <body>
    
    <section>
      <div id="logo">
        <img id="logo_img" src="assets/img/logo.png" alt="Xpress Health Logo">
      </div>
    </section>

    <nav class="navbar navbar-expand-lg fixed-top navbar-light">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="admin.php"><i class="fa fa-shopping-cart"></i> Orders <span class="badge badge-pill badge-success" style="margin-left:5px;"><?php echo $order_count ?></span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="reports.php"><i class="fa fa-info-circle"></i> Reports</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="clients.php"><i class="fa fa-users"></i> Clients</a>
          </li>
        </ul>
        <ul class="navbar-nav mr-right mt-2 mt-lg-0"> 
          <li class="nav-item">
            <a class="nav-link" href="admin.php"><i class="fa fa-user-md"></i> Administrator: <strong><?php echo $_SESSION['client_email'] ?></strong></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="logout();return false;"><i class="fa fa-sign-out"></i> Logout</a>
          </li> 
        </ul>
      </div>
    </nav>
    <section id="main">
      <div class="container-fluid" id="table-area">
        <?php if (!empty($clients)) { ?>
          <div class="row">
            <div class="col-12">
              <h4 class="table-header">Clients</h4>
            </div>
          </div>

          <table id="orders" class="table table-sm table-bordered table-hover" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th scope="col">Client ID</th>
                <th scope="col">Name</th>
                <th scope="col">Surname</th>
                <th scope="col" width="15%">Tel (Home)</th>
                <th scope="col" width="15%">Tel (Work)</th>
                <th scope="col" width="15%">Cell</th>
                <th scope="col">Email</th>
                <th scope="col">Registered</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($clients as $client) : ?>
              <tr>
                <th scope="row"><?php echo $client['client_id'] ?></th>
                <td><?php echo $client['client_name'] ?></td>
                <td><?php echo $client['client_surname'] ?></td>
                <td width="15%"><?php echo $client['client_tel_home'] ?></td>
                <td width="15%"><?php echo $client['client_tel_work'] ?></td>
                <td width="15%"><?php echo $client['client_tel_cell'] ?></td>
                <td><?php echo $client['client_email'] ?></td>
                <td align="center"><?php echo (!empty($client['pass'])) ? "<i class='text-success fa fa-lg fa-check'></i>" : "<i class='text-danger fa fa-lg fa-times'></i>" ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php } else { ?>
          <h4 class="no_orders">No client data found</h4>
        <?php }  ?>
      </div>
    </section>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    <script src="assets/libraries/noty/lib/noty.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/libraries/DataTables/datatables.min.js"></script>
    <script src="assets/js/login.js"></script>
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/admin.js"></script>
    

  </body>
</html>