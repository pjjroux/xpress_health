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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include database class  
require_once('Database.php'); 

require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/fpdf/fpdf.php');

require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/Exception.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/PHPMailer.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/SMTP.php');

class Invoice {
    protected $inv_num, $inv_date, $client_id, $consultation, $total_supplement, $grand_total;

    protected $invoice_lines, $order_status;

    protected $client_name, $client_surname, $client_address, $client_postalcode, $client_tel_cell, $client_email;

    protected $database;

    protected $error;


    /**
     * Create new Invoice object
     */
    public function __construct($inv_num = null) {
        $this->database = new Database(); 

        if (!is_null($inv_num)) {
            $this->set_invoice_data($inv_num);
        }

    }

    /**
     * Get Invoice data from database tables and set properties
     * @param string $inv_num Invoice number
     */
    public function set_invoice_data($inv_num) {
        $this->database->query('
            SELECT * FROM invoices 
            INNER JOIN invoice_lines on invoices.inv_num = invoice_lines.inv_num 
            INNER JOIN clients on invoices.client_id = clients.client_id
            WHERE invoices.inv_num = :inv_num
            ');
        $this->database->bind(':inv_num', $inv_num);
        $data = $this->database->resultset();

        if (!empty($data)) {
            $this->inv_num = $data[0]['inv_num'];
            $this->inv_date = $data[0]['inv_date'];
            $this->client_id = $data[0]['client_id'];
            $this->consultation = $data[0]['consultation'];
            $this->total_supplement = $data[0]['total_supplement'];
            $this->grand_total = $data[0]['grand_total'];
            $this->client_name = $data[0]['client_name'];
            $this->client_surname = $data[0]['client_surname'];
            $this->client_address = $data[0]['client_address'];
            $this->client_postalcode = $data[0]['client_postalcode'];
            $this->client_tel_cell = $data[0]['client_tel_cell'];
            $this->client_email = $data[0]['client_email'];

           $i=0;
           foreach ($data as $line) {
               $this->invoice_lines[$i]['supplement_id'] = $line['supplement_id'];
               $this->invoice_lines[$i]['price_charged'] = $line['price_charged'];
               $this->invoice_lines[$i]['quantity'] = $line['quantity'];
               $this->invoice_lines[$i]['total'] = $line['total'];
               $i++;
           }

           $this->database->query('SELECT * FROM orders_awaiting_payment WHERE inv_num = :inv_num');
           $this->database->bind(':inv_num', $inv_num);
           $row = $this->database->single();
   
           if (!empty($row)) {
                $this->order_status = 'Pending';
           } else {
                $this->order_status = 'Completed';
           } 
       }
    }

    /**
     * Create invoice pdf document using FPDF and send email to client
     */
    public function create_and_email() {
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);

        $pdf->Image('../assets/img/logo.png',20,16,40);
    
        //Cell(width , height , text , border , end line , [align] )

        $pdf->Cell(130 ,5,'',0,0);

        if ($this->order_status != 'Completed') {
            $pdf->Cell(59 ,5,'PRO FORMA INVOICE',0,1);//end of line
        } else {
            $pdf->Cell(59 ,5,'INVOICE',0,1);//end of line
        }
       
        //set font to arial, regular, 12pt
        $pdf->SetFont('Arial','',12);

        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(59 ,5,'',0,1);//end of line

        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(25 ,5,'Date ',0,0);
        $pdf->Cell(34 ,5,$this->inv_date,0,1);//end of line

        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(25 ,5,'Invoice # ',0,0);
        $pdf->Cell(34 ,5,$this->inv_num,0,1);//end of line

        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(25 ,5,'Customer ID ',0,0);
        $pdf->Cell(34 ,5,$this->client_id,0,1);//end of line

        //make a dummy empty cell as a vertical spacer
        $pdf->Cell(189 ,10,'',0,1);//end of line

        //billing address
        $pdf->Cell(100 ,5,'Bill to',0,1);//end of line

        //add dummy cell at beginning of each line for indentation
        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_name. ' '. $this->client_surname ,0,1);

        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_address,0,1);

        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_postalcode,0,1);

        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_tel_cell,0,1);

        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_email,0,1);

        //make a dummy empty cell as a vertical spacer
        $pdf->Cell(189 ,10,'',0,1);//end of line

        $pdf->Cell(30 ,5,'Order status:',0,0);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(30 ,5,$this->order_status,0,1);

        $pdf->Ln();

        //invoice contents
        $pdf->Cell(90 ,5,'Supplement',1,0);
        $pdf->Cell(30 ,5,'Price',1,0);
        $pdf->Cell(25 ,5,'Quantity',1,0);
        $pdf->Cell(34 ,5,'Amount',1,1);//end of line

        $pdf->SetFont('Arial','',12);

        //invoice lines
        foreach ($this->invoice_lines as $line) {
            $pdf->Cell(90 ,5,$line['supplement_id'],1,0);
            $pdf->Cell(30 ,5,$line['price_charged'],1,0,'R');
            $pdf->Cell(25 ,5,$line['quantity'],1,0,'C');
            $pdf->Cell(34 ,5,$line['total'],1,1,'R');//end of line
        }

        $pdf->Cell(120 ,5,'',0,0);
        $pdf->Cell(25 ,5,'Total Due',0,0);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(5 ,5,'R',1,0);
        $pdf->Cell(29 ,5,$this->grand_total,1,1,'R');//end of line
        
        $pdf->Ln();

        $pdf->SetFont('Arial','',12);

        if ($this->order_status != 'Completed') {
            $pdf->Cell(100 ,5,'Make EFT payment to:',0,1);
            $pdf->Ln();
    
            $pdf->Cell(100 ,5,'Xpress Health Wellness Center',0,1);
            $pdf->Cell(100 ,5,'Bank: ABSA',0,1);
            $pdf->Cell(100 ,5,'Account number: 45124561254',0,1);						
            $pdf->Cell(100 ,5,'Account type: Cheque',0,1);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(100 ,5,'SMS proof of payment to:  0824712929 (use INV number as reference): '.$this->inv_num,0,1);	
        } else {
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(100 ,5,'Payment received and order completed',0,1);
        }
						
        // Output document as string and send email
        $pdfdoc = $pdf->Output('S');

        $this->send_mail($pdfdoc);
    }

    /**
     * Send email using PHPMailer
     * @param string $attachment FPDF document string for attachment
     */
    public function send_mail($attachment) {                
        try {
            $mail = new PHPMailer(true); 

            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = "xpresshealth000@gmail.com";
            $mail->Password = "Xpress123";

            //Recipients
            $mail->setFrom('xpresshealth000@gmail.com', 'Xpress Health');
            $mail->addAddress($this->client_email, $this->client_name . ' '. $this->client_surname);     

            //Attachments
            $mail->addStringAttachment($attachment, $this->inv_num.'.pdf');

            //Content
            $mail->isHTML(true);                      
            $mail->Subject = 'Xpress Health Invoice: '. $this->inv_num;
            $mail->Body    = 'Please find attached invoice for your supplement order.';
            $mail->AltBody = 'Please find attached invoice for your supplement order.';

            $mail->send();
        } catch (Exception $e) {
            $this->error = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }

    /**
     * Send email using PHPMailer to Admin user with order details
     * 
     */
    public function send_mail_admin() {                
        try {
            //Get admin user details
            $this->database->query('SELECT * FROM clients WHERE admin = 1');
            $row = $this->database->single();

            // Send mail
            $mail = new PHPMailer(true); 

            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = "xpresshealth000@gmail.com";
            $mail->Password = "Xpress123";

            //Recipients
            $mail->setFrom('xpresshealth000@gmail.com', 'Xpress Health');
            $mail->addAddress($row['client_email'], $row['client_name']. ' '. $row['client_surname']);     

            //Content
            $mail->isHTML(true);                      
            $mail->Subject = 'Customer order placed: '. $this->inv_num;
            $mail->Body    = 'Customer ID: <strong>'.$this->client_id. '</strong> placed an order for the amount of R'. number_format($this->grand_total, 2, '.', ' ');
            $mail->AltBody = 'Customer ID: '.$this->client_id. ' placed an order for the amount of R'. number_format($this->grand_total, 2, '.', ' ');

            $mail->send();
        } catch (Exception $e) {
            $this->error = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }

    /**
     *  Create picking slip for Admin email
     */
    function create_shipping_label() {
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
    
        $pdf->Image('../assets/img/logo.png',20,16,40);
    
        $pdf->Cell(130 ,5,'',0,0);
    
        $pdf->Cell(59 ,5,'PICKING SLIP',0,1);//end of line
    
        //set font to arial, regular, 12pt
        $pdf->SetFont('Arial','',12);
    
        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(59 ,5,'',0,1);//end of line
    
        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(25 ,5,'Date ',0,0);
        $pdf->Cell(34 ,5,$this->inv_date,0,1);//end of line
    
        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(25 ,5,'Invoice # ',0,0);
        $pdf->Cell(34 ,5,$this->inv_num,0,1);//end of line
    
        $pdf->Cell(130 ,5,'',0,0);
        $pdf->Cell(25 ,5,'Customer ID ',0,0);
        $pdf->Cell(34 ,5,$this->client_id,0,1);//end of line
    
        //make a dummy empty cell as a vertical spacer
        $pdf->Cell(189 ,10,'',0,1);//end of line
    
        //billing address
        $pdf->Cell(100 ,5,'Deliver to',0,1);//end of line
    
        //add dummy cell at beginning of each line for indentation
        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_name. ' '. $this->client_surname ,0,1);
    
        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_address,0,1);
    
        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_postalcode,0,1);
    
        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_tel_cell,0,1);
    
        $pdf->Cell(10 ,5,'',0,0);
        $pdf->Cell(90 ,5,$this->client_email,0,1);
    
        //make a dummy empty cell as a vertical spacer
        $pdf->Cell(189 ,10,'',0,1);//end of line
    
        $pdf->Ln();
    
        //invoice contents
        $pdf->Cell(90 ,5,'Supplement',1,0);
        $pdf->Cell(25 ,5,'Quantity',1,1);

    
        $pdf->SetFont('Arial','',12);
    
        //invoice lines
        foreach ($this->invoice_lines as $line) {
            $pdf->Cell(90 ,5,$line['supplement_id'],1,0);
            $pdf->Cell(25 ,5,$line['quantity'],1,1,'C');
        }
            
        // Output document as string and send email
        $pdfdoc = $pdf->Output('S');
    
        $this->send_mail_picking($pdfdoc);
    }

    /**
     * Send email using PHPMailer
     * @param string $attachment FPDF document string for attachment
     */
    public function send_mail_picking($attachment) {                
        try {
            //Get admin user details
            $this->database->query('SELECT * FROM clients WHERE admin = 1');
            $row = $this->database->single();

            $mail = new PHPMailer(true); 

            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = "xpresshealth000@gmail.com";
            $mail->Password = "Xpress123";

            //Recipients
            $mail->setFrom('xpresshealth000@gmail.com', 'Xpress Health');
            $mail->addAddress($row['client_email'], $row['client_name']. ' '. $row['client_surname']);     

            //Attachments
            $mail->addStringAttachment($attachment, $this->inv_num.'.pdf');

            //Content
            $mail->isHTML(true);                      
            $mail->Subject = 'Xpress Health Picking Slip: '. $this->inv_num;
            $mail->Body    = 'Please find attached picking slip for Custumer ID: <strong>'.$this->client_id.'</strong>';
            $mail->AltBody = 'Please find attached picking slip for Custumer ID: '.$this->client_id;

            $mail->send();
        } catch (Exception $e) {
            $this->error = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }

    /**
     * Return error property
     */
    public function get_error() {
        return $this->error;
    }

    /**
     * Return Invoice lines
     */
    public function get_invoice_lines() {
        return $this->invoice_lines;
    }

}




?>

