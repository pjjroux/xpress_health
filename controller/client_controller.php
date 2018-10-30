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
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include Client class  
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Client.php');

/**
 * Decide on action using $_GET parameter
 */
$action = (isset($_GET['action'])) ? $_GET['action'] : null ;
switch ($action) {
    case 'getClientData':
        $client_id = (isset($_GET['client_id'])) ? $_GET['client_id'] : null ;
        getClientData($client_id);
        break;
    case 'getReferences':
        getReferences();
        break;
    case 'registerClient':
        registerClient($_POST);
        break;
    case 'updateClient':
        updateClient($_POST);
        break;
    case 'isAlreadyRegistered':
        isAlreadyRegistered($_GET['client_id'], $_GET['client_email']);
        break;
    case 'isAlreadyRegisteredUpdate':
        isAlreadyRegisteredUpdate($_GET['client_id'], $_GET['client_email']);
        break;
    case 'oldPasswordValid':
        oldPasswordValid($_GET['old_password']);
        break;
    case 'updatePassword':
        updatePassword();
        break;
}


/**
 * Retrieve client data and return json
 * 
 * @param string $client_id Client ID number
 * @return json $data
 */
function getClientData($client_id = null) {
    if (is_null($client_id)) {
        $client = new Client($_SESSION['client_id']);
    } else {
        $client = new Client($client_id);
    }

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

    header("Location: ../register.html?registered=1");
}

/**
 * Update client information
 * Save data to mysql and reload
 * 
 * @param array $form_data Submitted data from account form
 * @return void
 */
function updateClient($form_data) {
    $client = new Client($form_data['inputID']);

    $client->update_client($form_data);
    header("Location: ../account.php?updated=1");
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
    
        $database->query('SELECT * FROM clients INNER JOIN auth on clients.client_id = auth.client_id WHERE client_email = :client_email'); 
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

/**
 * Test if client not already registered by testing client_id and client_email seperately for update on account data
 * 
 * @param string $client_id Client ID number
 * @param string $client_email Client email address
 * @return json $data Registered status
 */
function isAlreadyRegisteredUpdate($client_id, $client_email) {
    $data['client_email'] = false;
    $data['client_id'] = false;

    $database = new Database();

    // Client see if client_id exists if changed
    if ($client_id != $_SESSION['client_id']) {
        $database->query('SELECT * FROM clients WHERE client_id = :client_id');
        $database->bind(':client_email', $client_id);
        $row = $database->single();
        
        if (!empty($row)) {
            $data['client_id'] = true;
        } else {
            $data['client_id'] = false;
        }
    }

    // Client see if client_email exists if changed
    if ($client_email != $_SESSION['client_email']) {
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


/**
 * Get all clients in database
 */
function getClients() {
    $database = new Database();

    $database->query('
        SELECT 
            clients.client_id,
            clients.client_name,
            clients.client_surname,
            clients.client_address,
            clients.client_postalcode,
            clients.client_tel_home,
            clients.client_tel_work,
            clients.client_tel_cell,
            clients.client_email,
            auth.pass
        FROM clients 
            LEFT JOIN auth on auth.client_id = clients.client_id
        WHERE clients.admin = 0
    ');

    $data = $database->resultset();

    return $data;
}


/** 
 * Update client password
 */
function updatePassword() {
    $client = new Client($_SESSION['client_id']);

    $client->update_client_password(password_hash($_POST['inputPassword'], PASSWORD_DEFAULT));

    session_start();
    session_destroy();

    header("Location: ../login.html?updated_password=1");
}


/**
 * Validate old password before update
 */
function oldPasswordValid($old_password) {
    $database = new Database();
    
    $database->query('SELECT * FROM auth INNER JOIN clients on auth.client_id = clients.client_id WHERE client_email = :client_email'); 
    $database->bind(':client_email', $_SESSION['client_email']);
    $row = $database->single();

    if (!empty($row)) {
        // Validate password
        if (password_verify($old_password, $row['pass'])) {
            $data['valid'] = true;
        } else {
            $data['valid'] = false;
        }
    }

    echo json_encode($data);
}

?>

