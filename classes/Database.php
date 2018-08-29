<?php
/*
|--------------------------------------------------------------------------
| Database handler class 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-08-27
|
*/

// Define configuration  
define("DB_HOST", "localhost");  
define("DB_USER", "xpress_admin");  
define("DB_PASS", "leyvosYRnFHPujCH");  
define("DB_NAME", "xpress_health"); 

/**
 *  Database class
 *  Represents a database connection and all database queries
 */
class Database {  
    private $host = DB_HOST;  
    private $user = DB_USER;  
    private $pass = DB_PASS;  
    private $dbname = DB_NAME;
    
    private $dbh;  
    private $error;
    
    /**
     * Open connection to database and set PDO object
     */
    public function __construct() {  
        // Set DSN  
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;  

        // Set options  
        $options = array(  
            PDO::ATTR_PERSISTENT => true,  
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION  
        );  

        // Create a new PDO instanace  
        try {  
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);  
        } catch(PDOException $e) {  
            $this->error = $e->getMessage();  
        }  
    }  

    /**
     * Prepares PDO SQL query
     * 
     * @param string $query SQL string query
     */
    public function query($query) {  
        $this->stmt = $this->dbh->prepare($query);  
    }
    
    /**
     * Binds value to prepared statement by type
     * 
     * @param string $param Named parameter value
     * @param mixed $value Value to bind to named parameter
     * @param mixed $type Type of value sent in $value
     */
    public function bind($param, $value, $type = null) {  
        if (is_null($type)) {  
            switch (true) {  
                case is_int($value):  
                    $type = PDO::PARAM_INT;  
                    break;  
                case is_bool($value):  
                    $type = PDO::PARAM_BOOL;  
                    break;  
                case is_null($value):  
                    $type = PDO::PARAM_NULL;  
                    break;  
                default:  
                    $type = PDO::PARAM_STR;  
            }  
        }  
        $this->stmt->bindValue($param, $value, $type);  
    }
    
    /**
     * Execute prepared statement query
     */
    public function execute() {  
        return $this->stmt->execute();  
    }
    
    /**
     * Returns executed results in array
     * 
     * @return array Query results
     */
    public function resultset() {  
        $this->execute();  
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);  
    }
    
    /**
     * Returns single record from query result
     * 
     * @return mixed Single query result
     */
    public function single() {  
        $this->execute();  
        return $this->stmt->fetch(PDO::FETCH_ASSOC);  
    } 

    /**
     * Returns the number of rows affected by prepared query
     * 
     * @return int Row count
     */
    public function rowCount() {  
        return $this->stmt->rowCount();  
    }
    
    /**
     * Returns the id of last record inserted
     * 
     * @return mixed Primary key of last insterted record
     */
    public function lastInsertId() {  
        return $this->dbh->lastInsertId();  
    }
    
    /**
     * Start database transaction
     */
    public function beginTransaction() {  
        return $this->dbh->beginTransaction();  
    }

    /**
     * End database transaction and commit changes
     */
    public function endTransaction() {  
        return $this->dbh->commit();  
    }
    
    /**
     * Cancel database transaction and rollback changes
     */
    public function cancelTransaction() {  
        return $this->dbh->rollBack();  
    }
    
    /**
     * Dump current query for debugging purposes
     */
    public function debugDumpParams() {  
        return $this->stmt->debugDumpParams();  
    }  
} 
?>