<?php
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/controller/order_controller.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Report.php');

// If admin user is not logged in redirect to index
if (!isset($_SESSION['client_name'])) { 
    header('Location: index.php');
}
  
// Get outstanding orders
$orders = getOrders();
$order_count = (!empty($orders)) ? count($orders) : 0 ;


$report = new Report();

$report_type = (isset($_GET['report'])) ? $_GET['report'] : null ;
$report_data = [];
$report_heading = '';

switch ($report_type) {
    case 'low_stock':
        $report_data = $report->get_low_stock_levels();
        $report_heading = 'Low Stock Levels';
        break;
    case 'top_10_sold':
        $report_data = $report->get_top_10_sold();
        $report_heading = 'Top 10 Supplements';
        break;
    case 'top_suppliers':
        $report_data = $report->top_suppliers();
        $report_heading = 'Top Suppliers';
        break;
    case 'top_10_profit':
        $report_data = $report->top_10_profit();
        $report_heading = 'Top 10 Most Profitable Supplements';
        break;
    case 'select_dates':
        $report_heading = 'Select range for invoice enquiry';
        break;
    case 'get_sold_in_range':
        $report_data = $report->get_sold_in_range($_POST['start_date'], $_POST['end_date']);
        $report_heading = 'Invoices for period: ' . $_POST['start_date'] . ' to '. $_POST['end_date'];
        break;
    
}



// echo '<pre>';
// print_r($report_data);
// echo '</pre>';



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Xpress Health - Reports">
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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">

    <title>Xpress Health - Reports</title>

    <script>
        var report_type = '<?php echo $report_type ?>'; 
        if (report_type != null) {
            var report_data = '<?php echo json_encode($report_data,JSON_HEX_APOS) ?>';
        }
    </script>
  </head>
  <body>
  
    <section>
      <div id="logo">
        <img src="assets/img/logo.png" alt="Xpress Health Logo">
      </div>
    </section>

    <div align="center">
        <div class="btn-group" role="group" aria-label="Select a report">
            <a href="reports.php?report=select_dates" class="btn btn-success">Invoices</a>
            <a href="reports.php?report=low_stock" class="btn btn-success">Low Stock Levels</a>
            <a href="reports.php?report=top_10_sold" class="btn btn-success">Top 10 Supplements</a>
            <a href="reports.php?report=top_suppliers" class="btn btn-success">Top Suppliers</a>
            <a href="reports.php?report=top_10_profit" class="btn btn-success">Top 10 Most Profitable Supplements</a>
        </div>
    </div>


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

    <?php if (is_null($report_type)) { ?>
        <h4 class="no_orders">Please select a report</h4>
    <?php } else { ?>
        <h4 class="table-header"><?php echo $report_heading ?></h4>
    <?php } ?>


    <?php if ($report_type != 'select_dates' && $report_type != 'get_sold_in_range') : ?>
        <div align="center">
            <div id="chart" style="height: 250px;width: 90%;"></div>
        </div>
    <?php endif; ?>

    <?php if ($report_type == 'low_stock') : ?>        
        <section id="main">
            <div class="container-fluid" id="table-area">
                <?php if (!empty($report_data)) { ?>
                    <table id="orders" class="table table-sm table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                        <th scope="col">Supplement</th>
                        <th scope="col">Supplier</th>
                        <th scope="col">In Stock</th>
                        <th scope="col">Min Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $data) : ?>
                        <tr>
                            <th scope="row"><?php echo $data['supplement_id'] ?></th>
                            <td><?php echo $data['supplier_name'] ?></td>
                            <td><?php echo $data['stock_levels'] ?></td>
                            <td><?php echo $data['min_levels'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                <?php } else { ?>
                    <h4 class="no_orders">No stock levels below minimum</h4>
                <?php }  ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($report_type == 'top_10_sold') : ?>
        <section id="main">
            <div class="container-fluid" id="table-area">
                <?php if (!empty($report_data)) { ?>
                    <table id="orders" class="table table-sm table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">Supplement</th>
                            <th scope="col">Description</th>
                            <th scope="col">Sold</th>
                            <th scope="col">Unit Cost</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $data) : ?>
                        <tr>
                            <th scope="row"><?php echo $data['supplement_id'] ?></th>
                            <td><?php echo $data['supplement_description'] ?></td>
                            <td><?php echo $data['total_quantity_sold'] ?></td>
                            <td align="right"><?php echo 'R ' . number_format($data['cost_client'], 2, '.', ' ') ?></td>
                            <td align="right"><?php echo 'R ' . number_format($data['total_sold'], 2, '.', ' ') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                <?php } else { ?>
                    <h4 class="no_orders">No supplements found</h4>
                <?php }  ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($report_type == 'top_suppliers') : ?>
        <section id="main">
            <div class="container-fluid" id="table-area">
                <?php if (!empty($report_data)) { ?>
                    <table id="orders" class="table table-sm table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">Supplier ID</th>
                            <th scope="col">Supplier Name</th>
                            <th scope="col">Supplies number of supplements</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $data) : ?>
                        <tr>
                            <th scope="row"><?php echo $data['supplier_id'] ?></th>
                            <td><?php echo $data['supplier_name'] ?></td>
                            <td align="right"><?php echo $data['number_supplements_supplied'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                <?php } else { ?>
                    <h4 class="no_orders">No suppliers found</h4>
                <?php }  ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($report_type == 'top_10_profit') : ?>
        <section id="main">
            <div class="container-fluid" id="table-area">
                <?php if (!empty($report_data)) { ?>
                    <table id="orders" class="table table-sm table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">Supplement</th>
                            <th scope="col">Description</th>
                            <th scope="col">Cost</th>
                            <th scope="col">Selling</th>
                            <th scope="col">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $data) : ?>
                        <tr>
                            <th scope="row"><?php echo $data['supplement_id'] ?></th>
                            <td><?php echo $data['supplement_description'] ?></td>
                            <td align="right"><?php echo 'R ' . number_format($data['cost_incl'], 2, '.', ' ') ?></td>
                            <td align="right"><?php echo 'R ' . number_format($data['cost_client'], 2, '.', ' ') ?></td>
                            <td align="right"><?php echo 'R ' . number_format($data['profit'], 2, '.', ' ') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                <?php } else { ?>
                    <h4 class="no_orders">No suppliers found</h4>
                <?php }  ?>
            </div>
        </section>
    <?php endif; ?> 


    <?php if ($report_type == 'select_dates') : ?>
    <section id="main">
        <div class="container-fluid" id="table-area">
            <form class="form-reports" id="reports_form" method="post" action="reports.php?report=get_sold_in_range" onsubmit="return validateFormReport()" >
                <div class="row">
                    <div class="col-6 col-lg-2"><label for="start_date">Starting from:</label></div>
                    <div class="col-6 col-lg-2"><input type="date" id="start_date" name="start_date" required/></div>
                    <div class="col-6 col-lg-2"><label for="end_date">Ending at:</label></div>
                    <div class="col-6 col-lg-2"><input type="date" id="end_date" name="end_date" required/></div>
                    <div class="col-6 col-lg-2"><button class="btn btn-success" type="submit"><i class="fa fa-check"></i> Submit</button></div>
                </div>
            </form>
        </div>
    </section>
    <?php endif; ?> 


    <?php if ($report_type == 'get_sold_in_range') : ?>
        <section id="main">
            <div class="container-fluid" id="table-area">
                <?php if (!empty($report_data)) { ?>
                    <table id="orders" class="table table-sm table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">Invoice #</th>
                            <th scope="col">Date</th>
                            <th scope="col">Customer ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $data) : ?>
                        <tr>
                            <th scope="row"><?php echo $data['inv_num'] ?></th>
                            <td><?php echo $data['inv_date'] ?></td>
                            <td><?php echo $data['client_id'] ?></td>
                            <td><?php echo $data['client_name']. ' '. $data['client_surname'] ?></td>
                            <td align="right"><?php echo 'R ' . number_format($data['grand_total'], 2, '.', ' ') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                <?php } else { ?>
                    <h4 class="no_orders">No invoice data found for period</h4>
                <?php }  ?>
            </div>
        </section>
    <?php endif; ?> 

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"></script>
    <script src="assets/libraries/noty/lib/noty.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/libraries/DataTables/datatables.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="assets/js/login.js"></script>
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/reports.js"></script>
    

  </body>
</html>