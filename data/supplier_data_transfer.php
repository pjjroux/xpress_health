<?php
/*
|--------------------------------------------------------------------------
| Script to transfer supplier data to new mySQL table 
|--------------------------------------------------------------------------
|
| Original supplier data was handled in excel. The supplier data sheet saved as csv
| is read line by line into an array and from there inserted into the relevant mysql
| tables.
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-06-10
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

  $csvFile = 'csv/supplier.csv';

  $supplierData = readCSV($csvFile);
  //dd($supplierData);

  if (isset($supplierData)) {
    // Get unique bank names and insert into banks table
    $banks = [];
    
    // Get unique account types and insert into account_types table
    $account_types = [];

    foreach ($supplierData as $supplier) {
      $banks[] = $supplier[6];
      $account_types[] = $supplier[9];
    }

    $banks_unique = array_unique($banks);
    $account_types_unique = array_unique($account_types);

    $stmt = $conn->prepare("INSERT INTO banks (bank_name) VALUES (:bank_name)");
    $stmt->bindParam(':bank_name', $bank_name);

    foreach ($banks_unique as $rs) {
      $sth = $conn->prepare('SELECT bank_name FROM banks WHERE bank_name = :input_bank_name');
      $sth->bindParam(':input_bank_name', $rs);
      $sth->execute();
      $exists = $sth->fetch(PDO::FETCH_ASSOC);

      if (empty($exists) && !empty($rs)) {
        $bank_name = $rs;
        $stmt->execute();
      }  
    }

    $stmt = $conn->prepare("INSERT INTO account_types (acc_type_description) VALUES (:acc_type_description)");
    $stmt->bindParam(':acc_type_description', $acc_type_description);

    foreach ($account_types_unique as $rs) {
      $sth = $conn->prepare('SELECT acc_type_description FROM account_types WHERE acc_type_description = :input_acc_type_description');
      $sth->bindParam(':input_acc_type_description', $rs);
      $sth->execute();
      $exists = $sth->fetch(PDO::FETCH_ASSOC);

      if (empty($exists) && !empty($rs)) {
        $acc_type_description = $rs;
        $stmt->execute();
      }  
    }

    // Insert rest of supplier data into suppliers table
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, bank_id, acc_type_id, supplier_branch_code, supplier_accnum, supplier_person, supplier_tel,
    supplier_tel_cell, supplier_fax, supplier_email, supplier_comments)
    VALUES (:supplier_name, :bank_id, :acc_type_id, :supplier_branch_code, :supplier_accnum, :supplier_person, :supplier_tel, :supplier_tel_cell, :supplier_fax,
    :supplier_email, :supplier_comments)");
    
    foreach ($supplierData as $key => $value) {
      // Get bank_id
      $sth = $conn->prepare('SELECT bank_id FROM banks WHERE bank_name = :bank_name');
      $sth->bindParam(':bank_name', $value[6]);
      $sth->execute();
      $bank_id = $sth->fetch(PDO::FETCH_ASSOC);

      // Get acc_type_id
      $sth = $conn->prepare('SELECT acc_type_id FROM account_types WHERE acc_type_description = :acc_type_description');
      $sth->bindParam(':acc_type_description', $value[9]);
      $sth->execute();
      $acc_type_id = $sth->fetch(PDO::FETCH_ASSOC);

      $insert_bank_id = (!empty($bank_id['bank_id'])) ? $bank_id['bank_id'] : NULL;
      $insert_acc_type_id = (!empty($acc_type_id['acc_type_id'])) ? $acc_type_id['acc_type_id'] : NULL;

      $stmt->bindParam(':supplier_name', $value[0]);
      $stmt->bindParam(':bank_id', $insert_bank_id);
      $stmt->bindParam(':acc_type_id', $insert_acc_type_id);
      $stmt->bindParam(':supplier_branch_code', $value[7]);
      $stmt->bindParam(':supplier_accnum', $value[8]);
      $stmt->bindParam(':supplier_person',$value[1]);
      $stmt->bindParam(':supplier_tel', $value[2]);
      $stmt->bindParam(':supplier_tel_cell', $value[3]);
      $stmt->bindParam(':supplier_fax', $value[4]);
      $stmt->bindParam(':supplier_email', $value[5]);
      $stmt->bindParam(':supplier_comments', $value[10]);

      $stmt->execute();
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