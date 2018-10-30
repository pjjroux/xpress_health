<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Include Database class  
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/classes/Database.php');

$db = new Database();

$backup_name = 'xpress_health_'.date('Y-m-d');
$db->EXPORT_DATABASE("localhost","xpress_admin","leyvosYRnFHPujCH", "xpress_health", false, $backup_name);

?>