<?php
/*
|--------------------------------------------------------------------------
| Script to transfer invoice data to new mySQL table 
|--------------------------------------------------------------------------
|
| Original invoice data was handled in excel. The invoice data sheet saved as csv
| is read line by line into an array and from there inserted into the relevant mysql
| tables.
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-06-18
|
*/

/**
 * Dump and die function to print_r 
 * with formatting
 */
function dd($value,$die=null) {
  echo '<pre>';
  print_r($value);
  echo '</pre>';
  
  if (!is_null($die)) {
    exit();
  }
}

/**
 * Reads csv file line by line and returns array
 */
function readCSV($csvFile) {
  if (file_exists($csvFile)) {
    $file_handle = fopen($csvFile, 'r');

    while (!feof($file_handle) ) {
      $line_of_text[] = fgetcsv($file_handle, 1024);
    }

    fclose($file_handle);
    return $line_of_text;
  } else {
    return null;
  }
}

// PDO DSN information
$servername = "localhost";
$username = "xpress_admin";
$password = "leyvosYRnFHPujCH";
$dbname = "xpress_health";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $csvFile = 'files/invoice.csv';

  $invoiceData = readCSV($csvFile);
  //dd($invoiceData,1);

  if (isset($invoiceData)) {
    // Insert rest of supplier data into suppliers table
    $stmt = $conn->prepare("INSERT INTO invoices (inv_num, inv_date, client_id, consultation, total_supplement, grand_total)
    VALUES (:inv_num, :inv_date, :client_id, :consultation, :total_supplement, :grand_total)");

    $stmt1 = $conn->prepare("INSERT INTO invoice_lines (inv_num, supplement_id, price_charged, quantity, total)
    VALUES (:inv_num, :supplement_id, :price_charged, :quantity, :total)");

    foreach ($invoiceData as $invoice) {
      // Check if client_id exists
      $sth = $conn->prepare('SELECT client_id FROM clients WHERE client_id = :client_id');
      $sth->bindParam(':client_id', $invoice[2]);
      $sth->execute();
      $client_id = $sth->fetch(PDO::FETCH_ASSOC);

      if (!empty($client_id['client_id'])) {
        $inv_num = $invoice[0];

        // Date
        $timestamp = strtotime($invoice[1]);
        $inv_date = date("Y-m-d", $timestamp);

        $client_id = $client_id['client_id'];
        $consultation = (!empty($invoice[3])) ? preg_replace("/[^0-9.]/", "", $invoice[3]) : 0.00;
        $total_supplement = (!empty($invoice[32])) ? preg_replace("/[^0-9.]/", "", $invoice[32]) : 0.00;
        $grand_total = (!empty($invoice[33])) ? preg_replace("/[^0-9.]/", "", $invoice[33]) : 0.00;

        $stmt->bindParam(':inv_num', $inv_num);
        $stmt->bindParam(':inv_date', $inv_date);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':consultation', $consultation);
        $stmt->bindParam(':total_supplement', $total_supplement);
        $stmt->bindParam(':grand_total',$grand_total);

        $stmt->execute();

        // Supplement 1
        if (!empty($invoice[4])) {
          $sth = $conn->prepare('SELECT supplement_id FROM supplements WHERE supplement_id = :supplement_id');
          $sth->bindParam(':supplement_id', $invoice[4]);
          $sth->execute();
          $supplement_id = $sth->fetch(PDO::FETCH_ASSOC);

          $price_charged = (!empty($invoice[5])) ? preg_replace("/[^0-9.]/", "", $invoice[5]) : 0.00;
          $total = (!empty($invoice[7])) ? preg_replace("/[^0-9.]/", "", $invoice[7]) : 0.00;

          $stmt1->bindParam(':inv_num', $inv_num);
          $stmt1->bindParam(':supplement_id', $supplement_id['supplement_id']);
          $stmt1->bindParam(':price_charged', $price_charged);
          $stmt1->bindParam(':quantity', $invoice[6]);
          $stmt1->bindParam(':total', $total);

          $stmt1->execute();     
        }

        // Supplement 2
        if (!empty($invoice[8])) {
          $sth = $conn->prepare('SELECT supplement_id FROM supplements WHERE supplement_id = :supplement_id');
          $sth->bindParam(':supplement_id', $invoice[8]);
          $sth->execute();
          $supplement_id = $sth->fetch(PDO::FETCH_ASSOC);

          $price_charged = (!empty($invoice[9])) ? preg_replace("/[^0-9.]/", "", $invoice[9]) : 0.00;
          $total = (!empty($invoice[11])) ? preg_replace("/[^0-9.]/", "", $invoice[11]) : 0.00;

          $stmt1->bindParam(':inv_num', $inv_num);
          $stmt1->bindParam(':supplement_id', $supplement_id['supplement_id']);
          $stmt1->bindParam(':price_charged', $price_charged);
          $stmt1->bindParam(':quantity', $invoice[10]);
          $stmt1->bindParam(':total', $total);

          $stmt1->execute();     
        }

        // Supplement 3
        if (!empty($invoice[12])) {
          $sth = $conn->prepare('SELECT supplement_id FROM supplements WHERE supplement_id = :supplement_id');
          $sth->bindParam(':supplement_id', $invoice[12]);
          $sth->execute();
          $supplement_id = $sth->fetch(PDO::FETCH_ASSOC);

          $price_charged = (!empty($invoice[13])) ? preg_replace("/[^0-9.]/", "", $invoice[13]) : 0.00;
          $total = (!empty($invoice[15])) ? preg_replace("/[^0-9.]/", "", $invoice[15]) : 0.00;

          $stmt1->bindParam(':inv_num', $inv_num);
          $stmt1->bindParam(':supplement_id', $supplement_id['supplement_id']);
          $stmt1->bindParam(':price_charged', $price_charged);
          $stmt1->bindParam(':quantity', $invoice[14]);
          $stmt1->bindParam(':total', $total);

          $stmt1->execute();     
        }

        // Supplement 4
        if (!empty($invoice[16])) {
          $sth = $conn->prepare('SELECT supplement_id FROM supplements WHERE supplement_id = :supplement_id');
          $sth->bindParam(':supplement_id', $invoice[16]);
          $sth->execute();
          $supplement_id = $sth->fetch(PDO::FETCH_ASSOC);

          $price_charged = (!empty($invoice[17])) ? preg_replace("/[^0-9.]/", "", $invoice[17]) : 0.00;
          $total = (!empty($invoice[19])) ? preg_replace("/[^0-9.]/", "", $invoice[19]) : 0.00;

          $stmt1->bindParam(':inv_num', $inv_num);
          $stmt1->bindParam(':supplement_id', $supplement_id['supplement_id']);
          $stmt1->bindParam(':price_charged', $price_charged);
          $stmt1->bindParam(':quantity', $invoice[18]);
          $stmt1->bindParam(':total', $total);

          $stmt1->execute();     
        }

        // Supplement 5
        if (!empty($invoice[20])) {
          $sth = $conn->prepare('SELECT supplement_id FROM supplements WHERE supplement_id = :supplement_id');
          $sth->bindParam(':supplement_id', $invoice[20]);
          $sth->execute();
          $supplement_id = $sth->fetch(PDO::FETCH_ASSOC);

          $price_charged = (!empty($invoice[21])) ? preg_replace("/[^0-9.]/", "", $invoice[21]) : 0.00;
          $total = (!empty($invoice[23])) ? preg_replace("/[^0-9.]/", "", $invoice[23]) : 0.00;

          $stmt1->bindParam(':inv_num', $inv_num);
          $stmt1->bindParam(':supplement_id', $supplement_id['supplement_id']);
          $stmt1->bindParam(':price_charged', $price_charged);
          $stmt1->bindParam(':quantity', $invoice[22]);
          $stmt1->bindParam(':total', $total);

          $stmt1->execute();     
        }

        
        // Supplement 6
        if (!empty($invoice[24])) {
          $sth = $conn->prepare('SELECT supplement_id FROM supplements WHERE supplement_id = :supplement_id');
          $sth->bindParam(':supplement_id', $invoice[24]);
          $sth->execute();
          $supplement_id = $sth->fetch(PDO::FETCH_ASSOC);

          $price_charged = (!empty($invoice[25])) ? preg_replace("/[^0-9.]/", "", $invoice[25]) : 0.00;
          $total = (!empty($invoice[27])) ? preg_replace("/[^0-9.]/", "", $invoice[27]) : 0.00;

          $stmt1->bindParam(':inv_num', $inv_num);
          $stmt1->bindParam(':supplement_id', $supplement_id['supplement_id']);
          $stmt1->bindParam(':price_charged', $price_charged);
          $stmt1->bindParam(':quantity', $invoice[26]);
          $stmt1->bindParam(':total', $total);

          $stmt1->execute();     
        }

        // Supplement 7
        if (!empty($invoice[28])) {
          $sth = $conn->prepare('SELECT supplement_id FROM supplements WHERE supplement_id = :supplement_id');
          $sth->bindParam(':supplement_id', $invoice[28]);
          $sth->execute();
          $supplement_id = $sth->fetch(PDO::FETCH_ASSOC);

          $price_charged = (!empty($invoice[29])) ? preg_replace("/[^0-9.]/", "", $invoice[29]) : 0.00;
          $total = (!empty($invoice[31])) ? preg_replace("/[^0-9.]/", "", $invoice[31]) : 0.00;

          $stmt1->bindParam(':inv_num', $inv_num);
          $stmt1->bindParam(':supplement_id', $supplement_id['supplement_id']);
          $stmt1->bindParam(':price_charged', $price_charged);
          $stmt1->bindParam(':quantity', $invoice[30]);
          $stmt1->bindParam(':total', $total);

          $stmt1->execute();     
        }
      } else {
        throw new Exception('Client id not found: ' . $invoice[2]);
      }  
    }
    echo 'Transfer successful';
  } else {
    throw new Exception('Problem reading ' . $csvFile);
  }
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
} catch(Throwable $t) {
  echo "Error: " . $t->getMessage() . '<br> File: ' . $t->getFile() . '<br> Line: ' . $t->getLine();
}

$conn = null;

?>