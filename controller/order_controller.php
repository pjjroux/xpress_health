<?php
/*
|-----------------------------------------------------------------------------
| Order controller - Handles all interaction with outstanding orders 
|-----------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-10-02
|
*/
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Include Product class  
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Product.php');

// Include Invoice class
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Invoice.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/fpdf/fpdf.php');

require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/Exception.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/PHPMailer.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/SMTP.php');

$action = (isset($_GET['action'])) ? $_GET['action'] : '' ;

/**
 * Decide on action using $_GET parameter
 */
switch ($action) {
  case 'getOrders':
    getOrders();
    break;
  case 'confirmOrder':
    confirmOrder($_GET['inv_num']);
    break;
  case 'cancelOrder':
    cancelOrder($_GET['inv_num']);
    break;
  case 'requestInvoiceMail':
    requestInvoiceMail($_GET['inv_num']);
    break;
}

/**
 * Get all outstading orders and the invoice detail
 * 
 * @return array $orders Outstanding orders
 */
function getOrders() {
  $database = new Database();

  $database->query('
    SELECT * FROM orders_awaiting_payment 
    INNER JOIN invoices on orders_awaiting_payment.inv_num = invoices.inv_num
    INNER JOIN clients on invoices.client_id = clients.client_id
  ');

  $data = $database->resultset();

  if (!empty($data)) {
    return $data;
  }
}

/**
 * Confirm order, update inventory quantities and email client with update and admin user with shipping label
 * 
 * @param string $inv_num Invoice number
 */
function confirmOrder($inv_num) {
  $data['error'] = '';

  try {
    $database = new Database();

    $invoice = new Invoice($inv_num);

    // Update inventory quantities
    $database->beginTransaction();

    $invoice_lines = $invoice->get_invoice_lines();
    foreach ($invoice_lines as $invoice_line) {
      $database->query(
        'UPDATE supplements SET
            stock_levels = (stock_levels - :quantity) 
        WHERE supplement_id = :supplement_id;'
      );

      $database->bind(':supplement_id', $invoice_line['supplement_id']);
      $database->bind(':quantity', $invoice_line['quantity']);

      $database->execute();
    }

    // Remove record from hold status
    $database->query('DELETE FROM orders_awaiting_payment WHERE inv_num = :inv_num');
    $database->bind(':inv_num', $inv_num);

    $database->execute();

    $database->endTransaction();

    $invoice->set_order_status();

    // Send updated final invoice to client
    $invoice->create_and_email();
    $invoice->create_shipping_label();

  } catch (Throwable $t) {
    $data['error'] = $t->getMessage();
  } catch (Exception $e) {
    $data['error'] = $e->getMessage();     
  }

  echo json_encode($data);
}

/**
 * Confirm order, update inventory quantities and email client with update and admin user with shipping label
 * 
 * @param string $inv_num Invoice number
 */
function cancelOrder($inv_num) {
  $data['error'] = '';

  try {
    send_cancel_notification($inv_num);

    $database = new Database();
    
    $database->beginTransaction();

    $database->query('DELETE FROM invoice_lines WHERE inv_num = :inv_num');
    $database->bind(':inv_num', $inv_num);

    $database->execute();

    $database->query('DELETE FROM invoices WHERE inv_num = :inv_num');
    $database->bind(':inv_num', $inv_num);

    $database->execute();

    $database->query('DELETE FROM orders_awaiting_payment WHERE inv_num = :inv_num');
    $database->bind(':inv_num', $inv_num);

    $database->execute();

    $database->endTransaction();
  } catch (Throwable $t) {
    $data['error'] = $t->getMessage();
  } catch (Exception $e) {
    $data['error'] = $e->getMessage();     
  }

  echo json_encode($data);
}


/**
 * Get orders for client by client_id
 * 
 * @param string $client_id Client id number
 * @return array $array Invoice data 
 */
function getOrdersByID($client_id) {
  $database = new Database();
  
  $database->query('SELECT * FROM invoices WHERE client_id = :client_id');
  $database->bind(':client_id', $client_id);

  $data = $database->resultset();

  if (!empty($data)) {
    return $data;
  }
}

/**
 * Get orders for client by client_id
 * 
 * @param string $client_id Client id number
 * @return array $array Invoice data 
 */
function requestInvoiceMail($inv_num) {
  $data['error'] = '';

  try {
    $invoice = new Invoice($inv_num);
    // Send updated final invoice to client
    $invoice->create_and_email();

  } catch (Throwable $t) {
    $data['error'] = $t->getMessage();
  } catch (Exception $e) {
    $data['error'] = $e->getMessage();     
  }

  echo json_encode($data);
}


/**
 * Cancel notification
 */
function send_cancel_notification($inv_num) {
  try {
    $invoice = new Invoice($inv_num);

    $mail = new PHPMailer(true); 

    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = "xpresshealth000@gmail.com";
    $mail->Password = "Xpress123";

    //Recipients
    $mail->setFrom('xpresshealth000@gmail.com', 'Xpress Health');
    $mail->addAddress($invoice->get_client_email(), $invoice->get_client_name() . ' '. $invoice->get_client_surname());     

    //Content
    $mail->isHTML(true);                      
    $mail->Subject = 'Xpress Health Invoice: '. $invoice->get_inv_num() . ' cancelled';
    $mail->Body    = 'Please note that your order has been cancelled.';
    $mail->AltBody = 'Please note that your order has been cancelled.';

    $mail->send();
  } catch (Exception $e) {
      $invoice->error = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
  }
}


?>