<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Emailing extends Myschoolgh {

    /**
     * Send an email
     * 
     * @param String $message
     * @param String $email
     * @param String $name
     */
    public function send_email($subject, $message, $email, $name) {

        try {
            
            //Create an instance; passing `true` enables exceptions
            $mailer = new PHPMailer(true);

            // configuration settings
            $config = (Object) array(
                'subject' => $subject,
                'headers' => "From: {$this->appName} - MySchoolGH.Com<".SMTP_USER."> \r\n Content-type: text/html; charset=utf-8",
                'Smtp' => true,
                'SmtpHost' => SMTP_HOST,
                'SmtpPort' => SMTP_PORT,
                'SmtpUser' => SMTP_USER,
                'SmtpPass' => SMTP_PASSWORD,
                'SmtpSecure' => 'ssl'
            );

            $mailer->addAddress($email, $name);

            // additional settings
            $mailer->SMTPDebug = SMTP::DEBUG_OFF;
            $mailer->isSMTP();
            $mailer->Host = $config->SmtpHost;
            $mailer->SMTPAuth = true;
            $mailer->Username = $config->SmtpUser;
            $mailer->Password = $config->SmtpPass;

            // set the port to sent the mail
            $mailer->Port = $config->SmtpPort;

            // set the user from which the email is been sent
            $mailer->setFrom(SMTP_FROM, $this->appName);

            // set the subject and message
            $mailer->Subject = $subject;
            $mailer->Body    = $message;
            $mailer->AltBody = strip_tags($message);

            // send the email
            return $mailer->send();

        } catch(Exception $e) {
            return false;
        }
        
    }
}
