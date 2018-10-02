<?php
/*
|--------------------------------------------------------------------------
| Client class - Represents a user on the website 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-08-28
|
*/

// Include database class  
require_once('Database.php'); 

class Client {
    protected $client_id, $client_name, $client_surname, $client_address, $client_postalcode;
    protected $client_tel_home, $client_tel_work, $client_tel_cell, $client_email, $ref_id, $admin;

    protected $database;

    public function __construct($client_id = null) {
        $this->database = new Database(); 

        if (!is_null($client_id)) {
            $this->set_client_data($client_id);
        }

    }

    /**
     * Retrieve a clients information from database and set properties
     */
    private function set_client_data($client_id) {
        $this->database->query('SELECT * FROM clients WHERE client_id = :client_id');
        $this->database->bind(':client_id', $client_id);
        $row = $this->database->single();
        
        if (!empty($row)) {
            $this->client_id = $row['client_id'];
            $this->client_name = $row['client_name'];
            $this->client_surname = $row['client_surname'];
            $this->client_address = $row['client_address'];
            $this->client_postalcode = $row['client_postalcode'];
            $this->client_tel_home = $row['client_tel_home'];
            $this->client_tel_work = $row['client_tel_work'];
            $this->client_tel_cell = $row['client_tel_cell'];
            $this->client_email = $row['client_email'];
            $this->ref_id = $row['ref_id'];
            $this->admin = $row['admin'];
        }
    }

    /**
     * Registers new client and inserts data but if client_id already exists updates
     * existing record and then inserts password.
     * 
     * @param array $client_data Array containing field data for insert
     * @return void
     */
    public function insert_new_client($client_data) {
        // If it is an existing client update current data
        $this->database->beginTransaction(); 

        if (!empty(($this->client_id))) {
            $this->database->query(
                'UPDATE clients SET
                    client_name = :client_name, 
                    client_surname = :client_surname, 
                    client_address = :client_address, 
                    client_postalcode = :client_postalcode,
                    client_tel_home = :client_tel_home, 
                    client_tel_work = :client_tel_work, 
                    client_tel_cell = :client_tel_cell, 
                    client_email = :client_email, 
                    ref_id = :ref_id
                WHERE client_id = :client_id;'
            );
        } else {
            // Insert new client data
            $this->database->query(
                'INSERT INTO clients (
                    client_id, client_name, client_surname, client_address, 
                    client_postalcode, client_tel_home, client_tel_work, 
                    client_tel_cell, client_email, ref_id
                ) 
                VALUES (
                    :client_id, :client_name, :client_surname, :client_address, 
                    :client_postalcode, :client_tel_home, :client_tel_work,
                    :client_tel_cell, :client_email, :ref_id
                );'
            ); 
        }

        $this->database->bind(':client_id', $client_data['inputID']);
        $this->database->bind(':client_name', $client_data['inputName']); 
        $this->database->bind(':client_surname', $client_data['inputSurname']); 
        $this->database->bind(':client_address', $client_data['inputAddress']); 
        $this->database->bind(':client_postalcode', $client_data['inputPostal']); 
        $this->database->bind(':client_tel_home', $client_data['inputTelHome']); 
        $this->database->bind(':client_tel_work', $client_data['inputTelWork']); 
        $this->database->bind(':client_tel_cell', $client_data['inputCell']);
        $this->database->bind(':client_email', $client_data['inputEmail']);
        $this->database->bind(':ref_id', $client_data['inputRef']);  
        
        $this->database->execute();  

        $this->database->query('INSERT INTO auth (client_id, pass) VALUES (:client_id, :pass);');

        $this->database->bind(':client_id', $client_data['inputID']);
        $this->database->bind(':pass', password_hash($client_data['inputPassword'], PASSWORD_DEFAULT));

        $this->database->execute();    

        $this->database->endTransaction();  
    }

    /**
     * Updates client information
     * 
     * @param array $client_data Array containing field data for update
     * @return void
     */
    public function update_client($client_data) {
        $this->database->beginTransaction(); 

        $this->database->query(
            'UPDATE clients SET
                client_id = :client_id,
                client_name = :client_name, 
                client_surname = :client_surname, 
                client_address = :client_address, 
                client_postalcode = :client_postalcode,
                client_tel_home = :client_tel_home, 
                client_tel_work = :client_tel_work, 
                client_tel_cell = :client_tel_cell, 
                client_email = :client_email, 
                ref_id = :ref_id
            WHERE client_id = :original_client_id;'
        );

        $this->database->bind(':client_id', $client_data['inputID']);
        $this->database->bind(':client_name', $client_data['inputName']); 
        $this->database->bind(':client_surname', $client_data['inputSurname']); 
        $this->database->bind(':client_address', $client_data['inputAddress']); 
        $this->database->bind(':client_postalcode', $client_data['inputPostal']); 
        $this->database->bind(':client_tel_home', $client_data['inputTelHome']); 
        $this->database->bind(':client_tel_work', $client_data['inputTelWork']); 
        $this->database->bind(':client_tel_cell', $client_data['inputCell']);
        $this->database->bind(':client_email', $client_data['inputEmail']);
        $this->database->bind(':ref_id', $client_data['inputRef']); 
        $this->database->bind(':original_client_id', $_SESSION['client_id']); 
        
        $this->database->execute();  

        if ($client_data['inputID'] != $_SESSION['client_id']) {
            $this->database->query('UPDATE auth SET client_id = :client_id WHERE client_id = :original_client_id;');
            $this->database->bind(':client_id', $client_data['inputID']);
            $this->database->bind(':original_client_id', $_SESSION['client_id']); 
    
            $this->database->execute(); 
        }

        $this->database->endTransaction();  

        $_SESSION['client_id'] = $client_data['inputID'];
        $_SESSION['client_name'] = $client_data['inputName'] . ' ' . $client_data['inputSurname'];
        $_SESSION['client_email'] = $client_data['inputEmail'];
    }

    /**
     * Get registration status
     * 
     * @return boolean Registered status
     */
    public function get_registration_status() {
        $this->database->query('SELECT * FROM auth WHERE client_id = :client_id');
        $this->database->bind(':client_id', $this->client_id);
        $row = $this->database->single();

        if (!empty($row)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_client_id() {
        return $this->client_id;
    }

    public function get_client_name() {
        return $this->client_name;
    }

    public function get_client_surname() {
        return $this->client_surname;
    }

    public function get_client_address() {
        return $this->client_address;
    }

    public function get_client_postalcode() {
        return $this->client_postalcode;
    }

    public function get_client_tel_home() {
        return $this->client_tel_home;
    }

    public function get_client_tel_work() {
        return $this->client_tel_work;
    }

    public function get_client_tel_cell() {
        return $this->client_tel_cell;
    }

    public function get_client_email() {
        return $this->client_email;
    }

    public function get_ref_id() {
        return $this->ref_id;
    }

    public function get_admin() {
        return $this->admin;
    }
}








?>