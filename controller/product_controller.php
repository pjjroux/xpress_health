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
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Product.php');

$action = (isset($_GET['action'])) ? $_GET['action'] : '' ;

/**
 * Decide on action using $_GET parameter
 */
switch ($action) {
    case 'searchProducts': 
        searchProducts($_GET['search_term']);
        break;
}


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
function getProducts($start_from, $limit_val) {
    $database = new Database();
    
    $database->query('SELECT supplement_id FROM supplements ORDER BY supplement_id ASC LIMIT :start_from, :limit_val');
    $database->bind(':start_from', $start_from);
    $database->bind(':limit_val', $limit_val);
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
 * Get number of products total
 */
function getProductsTotal() {
    $database = new Database();
    
    $database->query('SELECT COUNT(supplement_id) AS total_supplements FROM supplements');
    $row = $database->single();

    if (!empty($row)) {
       return $row['total_supplements'];
    }
}


/**
 * Get products by search term and direct to products page
 * 
 * @param string $search_term Search criteria
 */
function searchProducts($search_term) {
    $database = new Database();

    $database->query("
        SELECT supplements.supplement_id FROM supplements  
        INNER JOIN supplement_descriptions on supplement_descriptions.description_id = supplements.description_id
        WHERE supplements.supplement_id LIKE CONCAT('%', :search_term, '%') 
        OR supplement_descriptions.long_description LIKE CONCAT('%', :search_term, '%')
        OR supplement_descriptions.supplement_description LIKE CONCAT('%', :search_term, '%')
    ");

    $database->bind(':search_term', $search_term);
    $data = $database->resultset();

    echo json_encode($data);

}



?>