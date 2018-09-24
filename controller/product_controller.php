<?php
/*
|--------------------------------------------------------------------------
| Product controller - Handles all interaction with database and class 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-19
|
*/

// Include Product class  
require_once('classes/Product.php');


/**
 * Get the top 4 best selling products
 */
function getBestSellingProducts() {
    $database = new Database();
    
    $database->query('SELECT supplement_id, SUM(quantity) AS total_quantity_sold FROM invoice_lines GROUP BY supplement_id ORDER BY total_quantity_sold DESC LIMIT 4');
    $data = $database->resultset();

    if (!empty($data)) {
        foreach ($data as $value) {
            $product = new Product($value['supplement_id']);
            $array[] = $product->get_product_card();
        }
    }

    return $array;
}

/**
 * Get all the products
 */
function getProducts() {
    $database = new Database();
    
    $database->query('SELECT supplement_id FROM supplements ORDER BY RAND()');
    $data = $database->resultset();

    if (!empty($data)) {
        foreach ($data as $value) {
            $product = new Product($value['supplement_id']);
            $array[] = $product->get_product_card();
        }
    }

    return $array;
}


?>