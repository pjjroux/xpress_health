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
        $supplement_qty = (isset($_GET['supplement_qty'])) ? $_GET['supplement_qty'] : 1 ;
        addToCart($_GET['supplement_id'], $supplement_qty);
        break;
    case 'add_qty':
        add_qty($_GET['supplement_id']);
        break;
    case 'remove_qty':
        remove_qty($_GET['supplement_id']);
        break;
    case 'removeFromCart':
        removeFromCart($_GET['supplement_id']);
        break;
    case 'updateCart':
        updateCart($_GET['shopping_cart_qty']);
        break;
}

/**
 * Add product to shopping cart
 * 
 * @param string $supplement_id Supplement ID
 * @param int $supplement_qty Quantity
 * @return json $data
 */
function addToCart($supplement_id, $supplement_qty = 1) {
    $data['error'] = '';

    if (isset($_SESSION['cart'])) {
        $product = new Product($supplement_id);

        $item = [
            "supplement_id" => $product->get_supplement_id(),
            "cost" => $product->get_cost_client(),
            "cost_per_item" => $product->get_cost_client(),
            "description" => $product->get_long_description(),
            "img" => $product->get_product_img(),
            "qty" => $supplement_qty,
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
 * Add 1 to supplement quantity and return json
 * 
 * @param string $supplement_id Supplement ID
 * @return json $data
 */
function add_qty($supplement_id) {
    $data['error'] = '';

    try {
        $key = array_search($supplement_id, array_column($_SESSION['cart'], 'supplement_id'));

        $_SESSION['cart'][$key]['qty'] = $_SESSION['cart'][$key]['qty'] + 1;
        $_SESSION['cart'][$key]['cost'] = $_SESSION['cart'][$key]['cost_per_item'] * $_SESSION['cart'][$key]['qty'];
    } catch (Throwable $t) {
        $data['error'] = $t->getMessage();
    } catch (Exception $e) {
        $data['error'] = $e->getMessage();     
    }

    echo json_encode($data);
}

/**
 * Remove 1 from supplement quantity and return json
 * 
 * @param string $supplement_id Supplement ID
 * @return json $data
 */
function remove_qty($supplement_id) {
    $data['error'] = '';

    try {
        $key = array_search($supplement_id, array_column($_SESSION['cart'], 'supplement_id'));

        $_SESSION['cart'][$key]['qty'] = $_SESSION['cart'][$key]['qty'] - 1;
        $_SESSION['cart'][$key]['cost'] = $_SESSION['cart'][$key]['cost_per_item'] * $_SESSION['cart'][$key]['qty'];
    } catch (Throwable $t) {
        $data['error'] = $t->getMessage();
    } catch (Exception $e) {
        $data['error'] = $e->getMessage();     
    }

    echo json_encode($data);
}


/**
 * Remove item from cart
 * 
 * @param string $supplement_id Supplement ID
 * @return json $data
 */
function removeFromCart($supplement_id) {
    $data['error'] = '';

    try {
        $key = array_search($supplement_id, array_column($_SESSION['cart'], 'supplement_id'));
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    } catch (Throwable $t) {
        $data['error'] = $t->getMessage();
    } catch (Exception $e) {
        $data['error'] = $e->getMessage();     
    }

    echo json_encode($data);
}

?>