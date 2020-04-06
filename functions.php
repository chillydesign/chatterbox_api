<?php



function updated_at_string() {
  return  date('Y-m-d H:i:s');
}

include('functions_conversations.php');
include('functions_messages.php');
include('functions_users.php');


function send_php_mail($to, $subject, $content) {

  $mail = new PHPMailer\PHPMailer\PHPMailer(true);                 // Passing `true` enables exceptions

  try {
      //Server settings
      //$mail->SMTPDebug = 2;                   // Enable verbose debug output
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();                          // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';           // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                   // Enable SMTP authentication
      $mail->Username = MAIL_USERNAME;          // SMTP username
      $mail->Password = MAIL_PASSWORD;          // SMTP password
      $mail->SMTPSecure = 'tls';                // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;
      //Recipients
      $mail->setFrom('harvey.charles@gmail.com', 'Chatterbox');

      if ( is_array($to) ) {
          foreach ($to as $person) {
              $mail->addAddress( $person );
          }
      } else {
          $mail->addAddress( $to );     // Add a recipient
      }

      $mail->addReplyTo('harvey.charles@gmail.com', 'Chatterbox');

      //Content
      $mail->isHTML(true);  // Set email format to HTML
      $mail->Subject = $subject;
      $mail->Body    =  $content;
      $mail->AltBody = $content;
      $mail->send();
      return true;
  } catch (Exception $e) {
      return false;
  }

}



?>
