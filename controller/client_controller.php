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
    case 'registerClient':
        registerClient($_POST);
        break;
    case 'isAlreadyRegistered':
        isAlreadyRegistered($_GET['client_id'], $_GET['client_email']);
        break;
    default:
        echo 'Invalid action - ' . __LINE__ . ' - ' . __FILE__;
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
    $client = new Client($form_data['inputID']);

    $client->insert_new_client($form_data);

    header("Location: ../register.php?registered=1");
}


/**
 * Test if client not already registered by testing client_id and client_email seperately
 * 
 * @param string $client_id Client ID number
 * @param string $client_email Client email address
 * @return json $data Registered status
 */
function isAlreadyRegistered($client_id, $client_email) {
    $client = new Client($client_id);

    $registered_status = $client->get_registration_status();

    $data['client_id'] = $registered_status;
    $data['client_email'] = '';

    if (!$registered_status) {
        // Client id not registered test if email address not already in use
        $database = new Database();
    
        $database->query('SELECT * FROM clients WHERE client_email = :client_email');
        $database->bind(':client_email', $client_email);
        $row = $database->single();

        if (!empty($row)) {
            $data['client_email'] = true;
        } else {
            $data['client_email'] = false;
        }
    } 

    echo json_encode($data);    
}






?>

