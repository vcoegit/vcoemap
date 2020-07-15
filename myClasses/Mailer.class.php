<?php namespace myClasses;

require_once 'includes/config.php';

class Mailer{

    public function __construct(){

    }
    
    public function sendConfirmationMail(string $email, string $hash) : int{

    $mailbody = 'Bitte öffnen sie folgenden Link um ihre email-Adresse zu bestätigen. \n';

        try {
            // prepare email message
        
            // $message = \Swift_Message::newInstance()
            $message = (new \Swift_Message('Test of Swift Mailer'))
                // ->setSubject('Test of Swift Mailer')
                ->setFrom(['christian.schaefer@vcoe.at' => 'Christian Schäfer'])
                ->setTo('plastic_home@yahoo.com')
                ->setBody('This is a test of Swift Mailer');
            
            // echo $message->toString();
        
            $transport = (new \Swift_SmtpTransport($smtp_server, 587, 'tls'))
                            ->setUsername($username)
                            ->setPassword($password);
            $mailer = (new \Swift_Mailer($transport));
            $result = $mailer->send($message);
        
            if ($result){
                //echo "Number of emails sent:" . $result;
                return $result;
            }else{
                // echo "Could't send email";
                return 0;
            }
        
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

}

?>