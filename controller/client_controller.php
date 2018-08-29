<?php
/*
|--------------------------------------------------------------------------
| Client controller - Handles all interaction with database and class 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-08-29
|
*/

// Include Client class  
require_once('../classes/Client.php');

/**
 * Decide on action using $_GET parameter
 */
switch ($_GET['action']) {
    case 'getClientData':
        getClientData($_GET['client_id']);
        break;
    case 'getReferences':
        getReferences();
        break;
    default:
        echo 'Invalid action - ' . __LINE__ . ' - ' . __FILE__;
        break;
    case 'registerClient':
        registerClient($_POST);
        break;
}


/**
 * Retrieve client data and return json
 * 
 * @param string $client_id Client ID number
 * @return json $data
 */
function getClientData($client_id) {
    $client = new Client($client_id);

    $data = [];

    if (!empty($client->get_client_id())) {
        $data = [
            'client_id' => $client->get_client_id(),
            'client_name' => $client->get_client_name(),
            'client_surname' => $client->get_client_surname(),
            'client_address' => $client->get_client_address(),
            'client_postalcode' => $client->get_client_postalcode(),
            'client_tel_home' => $client->get_client_tel_home(),
            'client_tel_work' => $client->get_client_tel_work(),
            'client_tel_cell' => $client->get_client_tel_cell(),
            'client_email' => $client->get_client_email(),
            'ref_id' => $client->get_ref_id(),
        ];
    }

    echo json_encode($data);
}

/**
 * Retrieve references for client registration and return json
 * 
 * @return json $data
 */
function getReferences() {
    $database = new Database();
    
    $database->query('SELECT * FROM client_references');
    $data = $database->resultset();

    echo json_encode($data);
}

/**
 * Register a client
 * Save data to mysql and redirect to login page
 * 
 * @param array $form_data Submitted data from registration form
 * @return void
 */
function registerClient($form_data) {
    
}


?>