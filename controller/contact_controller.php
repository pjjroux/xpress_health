<?php
/*
|-----------------------------------------------------------------------------
| Contact controller - Handles the contact form for sending mail to company
|-----------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-10-22
|
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/Exception.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/PHPMailer.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/xpress_health/assets/libraries/PHPMailer/src/SMTP.php');

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
    $mail->setFrom('xpresshealth000@gmail.com', $_POST['email']);
    $mail->addAddress('xpresshealth000@gmail.com');     

    //Content
    $mail->isHTML(true);                      
    $mail->Subject = 'Web Form Contact from: ' . $_POST['name'] . ' - ' . $_POST['contact'];
    $mail->Body    = $_POST['message'];
    $mail->AltBody = $_POST['message'];

    $mail->send();
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
} finally {
    header("Location: ../contact.html?sent=1");
}

?>