<?php
namespace App;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

require '././vendor/autoload.php';

class SendResetPasswordMail
{
    public static function sendResetPasswordMail($email, $name)
    {
        $mail = new PHPMailer();

        $mail->isSMTP();

        $mail->CharSet = 'UTF-8';
        $mail->setLanguage('ru', '././vendor/phpmailer/phpmailer/language/');
        $mail->IsHTML(true);

        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        $mail->Host = 'smtp.gmail.com';

        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->SMTPSecure = 'tls';

        $mail->SMTPAuth = true;

        $mail->Username = 'buildtop.info@gmail.com';

        $mail->Password = 'kxmjnexioohbjmgz';

        $mail->setFrom('buildtop.info@gmail.com');
        $mail->addAddress($email);
        $mail->addReplyTo($email, $name);

        //Content
                                        
        $mail->Subject = 'Reset password mail';
        $body = '';
        if (trim(!empty($name)) && trim(!empty($email))) {
            $body .= '<p>Hello ' . $name . '! we are sending you a link to reset your password</p><br>';
            $body .= "<p><a href=\"http://filestorage/change_password?email=$email&first_name=$name\">here we go! reset your password!</a></p>";
        }

        $mail->Body =  $body;
        if (!$mail->send()) {
            $message = 'An error occurred while sending the message';
        } else {
            $message = 'we have sent you an email to reset your password';
        }

        return $message;
    }
}
