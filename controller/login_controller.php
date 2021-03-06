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
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include Client class  
require_once('../classes/Client.php');

/**
 * Decide on action using $_GET parameter
 */
switch ($_GET['action']) {
    case 'login':
        login();
        break;
    case 'logout':
        logout();
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
 * @return void
 */
function login() {
    $client = new Client($_POST['client_id']);

    $_SESSION['client_id'] = $_POST['client_id'];
    $_SESSION['client_name'] = $client->get_client_name() . ' ' . $client->get_client_surname();
    $_SESSION['client_email'] = $client->get_client_email();
    $_SESSION['cart'] = [];

    if ($client->get_admin() != 1) {
        header("Location: ../login.html?logged_in=1");
    } else {
        header("Location: ../admin.php");
    }
}

/**
 * Logout user and destory session
 * 
 * @return void
 */
function logout() {
    session_start();
    session_destroy();
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
            $data['error'] = '';
            $data['client_id'] = $row['client_id'];
        } else {
            $data['error'] = 'Incorrect password';
        }
    } else {
        // Client email not registered
        $data['error'] = 'No account associated with email address';
    }


    echo json_encode($data);    
}

?>