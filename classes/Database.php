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

    
    /**
     * Author @tazotodua (https://github.com/tazotodua);
     */
    function EXPORT_DATABASE($host,$user,$pass,$name,$tables=false, $backup_name=false) { 
        set_time_limit(3000); $mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
        $queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }	if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); } 
        $content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `".$name."`\r\n--\r\n\r\n\r\n";
        foreach($target_tables as $table){
            if (empty($table)){ continue; } 
            $result	= $mysqli->query('SELECT * FROM `'.$table.'`');  	$fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows; 	$res = $mysqli->query('SHOW CREATE TABLE '.$table);	$TableMLine=$res->fetch_row(); 
            $content .= "\n\n".$TableMLine[1].";\n\n";   $TableMLine[1]=str_ireplace('CREATE TABLE `','CREATE TABLE IF NOT EXISTS `',$TableMLine[1]);
            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
                while($row = $result->fetch_row())	{ //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 )	{$content .= "\nINSERT INTO ".$table." VALUES";}
                        $content .= "\n(";    for($j=0; $j<$fields_amount; $j++){ $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ;}  else{$content .= '""';}	   if ($j<($fields_amount-1)){$content.= ',';}   }        $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";}	$st_counter=$st_counter+1;
                }
            } $content .="\n\n\n";
        }
        $content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
        $backup_name = $backup_name ? $backup_name : $name.'___('.date('H-i-s').'_'.date('d-m-Y').').sql';
        ob_get_clean(); header('Content-Type: application/octet-stream');  header("Content-Transfer-Encoding: Binary");  header('Content-Length: '. (function_exists('mb_strlen') ? mb_strlen($content, '8bit'): strlen($content)) );    header("Content-disposition: attachment; filename=\"".$backup_name."\""); 
        echo $content; exit;
    }
} 
?>