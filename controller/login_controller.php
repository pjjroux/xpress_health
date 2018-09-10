<?php
/*
|--------------------------------------------------------------------------
| Login controller - Handles login credentials and actions 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-10
|
*/

// Include Client class  
require_once('../classes/Client.php');

/**
 * Decide on action using $_GET parameter
 */
switch ($_GET['action']) {
    case 'login':
        login($_GET['client_id']);
        break;
    case 'validateUserLogin':
        validateUserLogin($_GET['client_email'], $_GET['client_password']);
        break;
    default:
        echo 'Invalid action - ' . __LINE__ . ' - ' . __FILE__;
        break;
}


/**
 * Login user
 * 
 * @param string $client_id Client ID number
 * @return void
 */
function login($client_id) {
    
}


/**
 * Validate user credentials
 * 
 * @param string $client_email Client email address
 * @param string $client_password Client password
 * @return json $data
 */
function validateUserLogin($client_email, $client_password) {
    $database = new Database();
    
    $database->query('SELECT * FROM auth INNER JOIN clients on auth.client_id = clients.client_id WHERE client_email = :client_email'); 
    $database->bind(':client_email', $client_email);
    $row = $database->single();

    if (!empty($row)) {
        // Validate password
        if (password_verify($client_password, $row['pass'])) {
            //return true and client_id
        } else {
            // password invalid
        }
    } else {
        // Client email not registered
    }


    echo json_encode($data);    
}

?>