<?php
/*
|--------------------------------------------------------------------------
| Product class - Represents a product on the website 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-17
|
*/

// Include database class  
require_once('Database.php'); 

class Product {
    protected $supplement_id, $description, $long_description, $cost_excl, $cost_incl, $perc_inc;
    protected $cost_client, $supplier_id, $min_levels, $stock_levels, $nappi_code;

    protected $database;

    public function __construct($supplement_id = null) {
        $this->database = new Database(); 

        if (!is_null($supplement_id)) {
            $this->set_product_data($supplement_id);
        }

    }

    /**
     * Retrieve product information from database and set properties
     */
    private function set_product_data($supplement_id) {
        $this->database->query('SELECT * FROM clients WHERE client_id = :client_id');
        $this->database->bind(':client_id', $client_id);
        $row = $this->database->single();
        
        if (!empty($row)) {
            $this->client_id = $row['client_id'];
            $this->client_name = $row['client_name'];
            $this->client_surname = $row['client_surname'];
            $this->client_address = $row['client_address'];
            $this->client_postalcode = $row['client_postalcode'];
            $this->client_tel_home = $row['client_tel_home'];
            $this->client_tel_work = $row['client_tel_work'];
            $this->client_tel_cell = $row['client_tel_cell'];
            $this->client_email = $row['client_email'];
            $this->ref_id = $row['ref_id'];
        }
    }
}


?>