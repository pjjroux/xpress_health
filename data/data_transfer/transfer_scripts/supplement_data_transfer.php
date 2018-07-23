<?php
/*
|--------------------------------------------------------------------------
| Script to transfer supplement data to new mySQL table 
|--------------------------------------------------------------------------
|
| Original supplement data was handled in excel. The supplement data sheet saved as csv
| is read line by line into an array and from there inserted into the relevant mysql
| tables.
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-06-12
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

  $csvFile = 'files/supplement.csv';

  $supplementData = readCSV($csvFile);
  //dd($supplementData,1);

  if (isset($supplementData)) {
    // Get unique descriptions and insert into supplement_descriptions table
    $descriptions = [];
    
    foreach ($supplementData as $supplement) {
      $descriptions[] = $supplement[2];
    }

    $descriptions_unique = array_unique($descriptions);

    $stmt = $conn->prepare("INSERT INTO supplement_descriptions (supplement_description) VALUES (:supplement_description)");
    $stmt->bindParam(':supplement_description', $supplement_description);

    foreach ($descriptions_unique as $rs) {
      $sth = $conn->prepare('SELECT supplement_description FROM supplement_descriptions WHERE supplement_description = :input_description');
      $sth->bindParam(':input_description', $rs);
      $sth->execute();
      $exists = $sth->fetch(PDO::FETCH_ASSOC);

      if (empty($exists) && !empty($rs)) {
        $supplement_description = $rs;
        $stmt->execute();
      }  
    }

    // Insert rest of supplement data into supplements table
    $stmt = $conn->prepare("INSERT INTO supplements (supplement_id, description_id, cost_excl, cost_incl, perc_inc, cost_client, supplier_id,
    min_levels, stock_levels, nappi_code)
    VALUES (:supplement_id, :description_id, :cost_excl, :cost_incl, :perc_inc, :cost_client, :supplier_id, :min_levels, :stock_levels,
    :nappi_code)");
    
    foreach ($supplementData as $key => $value) {
      // Get description_id
      $sth = $conn->prepare('SELECT description_id FROM supplement_descriptions WHERE supplement_description = :supplement_description');
      $sth->bindParam(':supplement_description', $value[2]);
      $sth->execute();
      $description_id = $sth->fetch(PDO::FETCH_ASSOC);

      // Get supplier_id
      $sth = $conn->prepare('SELECT supplier_id FROM suppliers WHERE supplier_name = :supplier_name');
      $sth->bindParam(':supplier_name', $value[7]);
      $sth->execute();
      $supplier_id = $sth->fetch(PDO::FETCH_ASSOC);

      $insert_supplier_id = (!empty($supplier_id['supplier_id'])) ? $supplier_id['supplier_id'] : NULL;
      $insert_description_id = (!empty($description_id['description_id'])) ? $description_id['description_id'] : NULL;

      // Set string values to decimal
      $insert_cost_excl = (!empty($value['3'])) ? preg_replace("/[^0-9,.]/", "", $value[3]) : 0.00;
      $insert_cost_incl = (!empty($value['4'])) ? preg_replace("/[^0-9,.]/", "", $value[4]) : 0.00;
      $insert_perc_inc = (!empty($value['5'])) ? preg_replace("/[^0-9,.]/", "", $value[5]) : 0.00;
      $insert_cost_client = (!empty($value['6'])) ? preg_replace("/[^0-9,.]/", "", $value[6]) : 0.00;

      $stmt->bindParam(':supplement_id', $value[1]);
      $stmt->bindParam(':description_id', $insert_description_id);
      $stmt->bindParam(':cost_excl', $insert_cost_excl);
      $stmt->bindParam(':cost_incl', $insert_cost_incl);
      $stmt->bindParam(':perc_inc', $insert_perc_inc);
      $stmt->bindParam(':cost_client', $insert_cost_client);
      $stmt->bindParam(':supplier_id', $insert_supplier_id);
      $stmt->bindParam(':min_levels', $value[8]);
      $stmt->bindParam(':stock_levels', $value[9]);
      $stmt->bindParam(':nappi_code', $value[10]);

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