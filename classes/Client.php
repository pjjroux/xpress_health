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
    protected $client_tel_home, $client_tel_work, $client_tel_cell, $client_email, $ref_id;

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
}








?>