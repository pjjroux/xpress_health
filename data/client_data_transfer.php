<?php
/*
|--------------------------------------------------------------------------
| Script to transfer client data to new mySQL table 
|--------------------------------------------------------------------------
|
| Original client data was handled in excel. The client data sheet saved as csv
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
function readCSV($csvFile){
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

  $csvFile = 'csv/client.csv';

  $clientData = readCSV($csvFile);

  if (isset($clientData)) {
    // Get unique client references and insert into client_references table
    $client_references = [];

    foreach ($clientData as $client) {
      $client_references[] = $client['10'];
    }

    $client_references_unique = array_unique($client_references);

    $stmt = $conn->prepare("INSERT INTO client_references (ref_description) VALUES (:unique_description)");
    $stmt->bindParam(':unique_description', $unique_description);

    foreach ($client_references_unique as $rs) {
      $sth = $conn->prepare('SELECT ref_description FROM client_references WHERE ref_description = :ref_description');
      $sth->bindParam(':ref_description', $rs);
      $sth->execute();
      $exists = $sth->fetch(PDO::FETCH_ASSOC);

      if (empty($exists) && !empty($rs)) {
        $unique_description = $rs;
        $stmt->execute();     
      }  
    }

    // Insert rest of client data into clients table
    $stmt = $conn->prepare("INSERT INTO clients (client_id, client_name, client_surname, client_address, client_postalcode, client_tel_home, client_tel_work,
    client_tel_cell, client_email, ref_id)
    VALUES (:client_id, :client_name, :client_surname, :client_address, :client_postalcode, :client_tel_home, :client_tel_work, :client_tel_cell, :client_email,
    :ref_id)");
    
    foreach ($clientData as $key => $value) {
      $sth = $conn->prepare('SELECT ref_id FROM client_references WHERE ref_description = :ref_description');
      $sth->bindParam(':ref_description', $value[10]);
      $sth->execute();
      $ref_id = $sth->fetch(PDO::FETCH_ASSOC);

      $insert_ref_id = (!empty($ref_id['ref_id'])) ? $ref_id['ref_id'] : NULL;

      $stmt->bindParam(':client_id',$value[1]);
      $stmt->bindParam(':client_name', $value[2]);
      $stmt->bindParam(':client_surname', $value[3]);
      $stmt->bindParam(':client_address', $value[4]);
      $stmt->bindParam(':client_postalcode', $value[5]);
      $stmt->bindParam(':client_tel_home', $value[6]);
      $stmt->bindParam(':client_tel_work', $value[7]);
      $stmt->bindParam(':client_tel_cell', $value[8]);
      $stmt->bindParam(':client_email', $value[9]);
      $stmt->bindParam(':ref_id', $insert_ref_id);

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