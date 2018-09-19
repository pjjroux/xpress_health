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
    protected $cost_client, $supplier_id, $min_levels, $stock_levels, $nappi_code, $product_img;

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
        $this->database->query('SELECT * FROM supplements INNER JOIN supplement_descriptions on supplements.description_id = supplement_descriptions.description_id WHERE supplement_id = :supplement_id');
        $this->database->bind(':supplement_id', $supplement_id);
        $row = $this->database->single();

        if (!empty($row)) {
            $this->supplement_id = $row['supplement_id'];
            $this->description = $row['supplement_description'];
            $this->long_description = $row['long_description'];
            $this->cost_excl = $row['cost_excl'];
            $this->cost_incl = $row['cost_incl'];
            $this->perc_inc = $row['perc_inc'];
            $this->cost_client = $row['cost_client'];
            $this->supplier_id = $row['supplier_id'];
            $this->min_levels = $row['min_levels'];
            $this->stock_levels = $row['stock_levels'];
            $this->nappi_code = $row['nappi_code'];
            $this->product_img = $row['img_path'];
        }
    }

    public function get_supplement_id() {
        return $this->supplement_id;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_long_description() {
        return $this->long_description;
    }

    public function get_cost_excl() {
        return $this->cost_excl;
    }

    public function get_cost_incl() {
        return $this->cost_incl;
    }

    public function get_perc_inc() {
        return $this->perc_inc;
    }

    public function get_cost_client() {
        return $this->cost_client;
    }

    public function get_supplier_id() {
        return $this->supplier_id;
    }

    public function get_min_levels() {
        return $this->min_levels;
    }

    public function get_stock_levels() {
        return $this->stock_levels;
    }

    public function get_nappi_code() {
        return $this->nappi_code;
    }

    public function get_product_img() {
        return $this->product_img;
    }

    public function get_product_card() {
        //Determine stock levels
        $stock_level = $this->get_stock_levels();

        if ($stock_level > 0 && $stock_level <= 10) {
            $stock_status = 'Limited stock';
            $stock_style = 'text-warning';
        } else if ($stock_level > 10) {
            $stock_status = 'In stock';
            $stock_style = 'text-success';
        } else {
            $stock_status = 'Out of stock';
            $stock_style = 'text-danger';
        }
        

        $product_card = [
            "supplement_id" => $this->get_supplement_id(),
            "long_description" => $this->get_long_description(),
            "stock_status" => $stock_status,
            "stock_style" => $stock_style,
            "cost" => 'R' . number_format($this->get_cost_client(), 2, '.', ' '),
            "description" => $this->get_description(),
            "img" => $this->get_product_img()
        ];

        return $product_card;
    }
}


?>