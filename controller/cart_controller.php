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

// Include Invoice class
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Invoice.php');

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
    case 'checkout':
        checkout();
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

/**
 * Checkout cart and create invoice, email confirmation sent to client and HCP/GA
 * 
 * @return json $data
 */
function checkout() {
    $data['error'] = '';

    try {
        $database = new Database();

        //Get invoice number by getting the last part of the INV number and adding 1
        $database->query('SELECT inv_num FROM invoices ORDER BY CAST(SUBSTRING(inv_num,4,20) as SIGNED INTEGER) DESC LIMIT 0,1');
        $row = $database->single();

        $int_portion = substr($row['inv_num'],3);
        $new_incremented = $int_portion + 1;

        $new_incremented = str_pad($new_incremented,strlen($int_portion),"0",STR_PAD_LEFT);

        $new_inv_num = 'INV' . $new_incremented;
        
        $total_price = array_sum(array_column($_SESSION['cart'], 'cost'));

        // Insert invoice lines
        $database->beginTransaction(); 

        // Insert invoice
        $database->query(
            'INSERT INTO invoices (
                inv_num, inv_date, client_id, total_supplement, grand_total
            ) 
            VALUES (
                :inv_num, :inv_date, :client_id, :total_supplement, :grand_total
            );'
        ); 

        $database->bind(':inv_num', $new_inv_num);
        $database->bind(':inv_date', date("Y-m-d"));
        $database->bind(':client_id', $_SESSION['client_id']); 
        $database->bind(':total_supplement', $total_price); 
        $database->bind(':grand_total', $total_price); 

        $database->execute();

        foreach ($_SESSION['cart'] as $cart_item) {
            $database->query(
                'INSERT INTO invoice_lines (
                    inv_num, supplement_id, price_charged, quantity, total
                ) 
                VALUES (
                    :inv_num, :supplement_id, :price_charged, :quantity, :total
                );'
            ); 

            $database->bind(':inv_num', $new_inv_num);
            $database->bind(':supplement_id', $cart_item['supplement_id']); 
            $database->bind(':price_charged', $cart_item['cost_per_item']); 
            $database->bind(':quantity', $cart_item['qty']); 
            $database->bind(':total', $cart_item['cost']); 

            $database->execute();
        }

        // Insert entry in orders awaiting payment for hold on stock levels
        $database->query(
            'INSERT INTO orders_awaiting_payment (inv_num) VALUES (:inv_num);'
        ); 
        $database->bind(':inv_num', $new_inv_num);

        $database->execute();

        $database->endTransaction();

        $_SESSION['cart'] = [];

        $invoice = new Invoice();
        $invoice->create_pdf();
    } catch (Throwable $t) {
        $data['error'] = $t->getMessage();
    } catch (Exception $e) {
        $data['error'] = $e->getMessage();     
    }

    echo json_encode($data);
}

?>