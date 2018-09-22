<?php
/*
|-----------------------------------------------------------------------------
| Shopping cart controller - Handles all interaction with database and class 
|-----------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-20
|
*/
session_start();

// Include Product class  
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Product.php');

$action = (isset($_GET['action'])) ? $_GET['action'] : '' ;

/**
 * Decide on action using $_GET parameter
 */
switch ($action) {
    case 'addToCart':
        addToCart($_GET['supplement_id']);
        break;
    case 'removeFromCart':
        removeFromCart($_GET['supplement_id']);
        break;
    case 'updateCart':
        updateCart($_GET['shopping_cart_qty']);
        break;
}

/**
 * Retrieve client data and return json
 * 
 * @param string $client_id Client ID number
 * @return json $data
 */
function addToCart($supplement_id) {
    $data['error'] = '';

    if (isset($_SESSION['cart'])) {
        $product = new Product($supplement_id);

        $item = [
            "supplement_id" => $product->get_supplement_id(),
            "cost" => $product->get_cost_client(),
            "cost_per_item" => $product->get_cost_client(),
            "description" => $product->get_long_description(),
            "img" => $product->get_product_img(),
            "qty" => 1,
        ];

        $key = array_search($item['supplement_id'], array_column($_SESSION['cart'], 'supplement_id'));
        if ($key !== false) {
            $_SESSION['cart'][$key]['qty'] = $_SESSION['cart'][$key]['qty'] + 1;
            $_SESSION['cart'][$key]['cost'] = $product->get_cost_client() * $_SESSION['cart'][$key]['qty'];
            
        } else {
            $_SESSION['cart'][] = $item;
        }

        
    } else {
        $data['error'] = 'Please login to continue';
    }

    echo json_encode($data);
}

/**
 * Retrieve client data and return json
 * 
 * @param string $client_id Client ID number
 * @return json $data
 */
function removeFromCart($supplement_id) {
    $data['error'] = '';

    try {
        $key = array_search($supplement_id, array_column($_SESSION['cart'], 'supplement_id'));
        unset($_SESSION['cart'][$key]);
    } catch (Throwable $t) {
        $data['error'] = $t->getMessage();
    } catch (Exception $e) {
        $data['error'] = $e->getMessage();     
    }

    echo json_encode($data);
}

/**
 * Retrieve client data and return json
 * 
 * @param array $shopping_cart_qty Array containing updated quantities
 * @return json $data
 */
function updateCart($shopping_cart_qty) {
    $data['error'] = '';

    try {
        foreach ($shopping_cart_qty as $key => $value) {
            $search_key = array_search($key, array_column($_SESSION['cart'], 'supplement_id'));
            $_SESSION['cart'][$key]['qty'] = $value;
        }
    } catch (Throwable $t) {
        $data['error'] = $t->getMessage();
    } catch (Exception $e) {
        $data['error'] = $e->getMessage();     
    }

    echo json_encode($data);
}

?>