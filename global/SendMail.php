<?php
use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;           
        use PHPMailer\PHPMailer\Exception;
class SendMail {
    public function send($conn,$mailCnt){
    global $conf;
    $mail= new PHPMailer(true);
    try {
    //Server settings
   $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       =$conf['smtp_host'] ;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $conf['smtp_user'];                     //SMTP username
    $mail->Password   = $conf['smtp_pass'];                               //SMTP password
    $mail->SMTPSecure = $conf['smtp_secure'];
                //Enable implicit TLS encryption
    $mail->Port       = $conf['smtp_port'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

   $mail->setFrom($mailCnt['from_email'], $mailCnt['from_name']);
   $mail->addAddress($mailCnt['to_email'], $mailCnt['to_name']);  
      //Add a recipient

      //content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = $mailCnt['subject'];
      $mail->Body    = $mailCnt['body'];
   
   $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
}