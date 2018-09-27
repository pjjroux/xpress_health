<?php
/*
|--------------------------------------------------------------------------
| Invoice class - Represents an invoice on the website 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-27
|
*/

// Include database class  
require_once('Database.php'); 

class Invoice {

    protected $database;

    public function __construct($inv_num = null) {
        $this->database = new Database(); 

        if (!is_null($inv_num)) {
            $this->set_invoice_data($inv_num);
        }

    }

    public function create_pdf() {
        require_once('../assets/libraries/fpdf/fpdf.php');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Hello World!');
        $pdf->Output();
    }

}




?>

