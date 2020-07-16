<?php namespace myClasses;

require_once 'vendor/autoload.php';

class Mailer{

    private $smtp_server;
    private $username;
    private $password;
    private $objEmail;

    public function __construct(Mail $objEmail){
        /**
         * Das könnte man auch in ein Konfigurationsfile auslagern...
         */
        $this->objEmail = $objEmail;
        $this->smtp_server = 'smtp.office365.com';
        $this->username = 'scc@vcoe.at';
        $this->password = '!14B6322050Jan';
    }
    
    public function sendConfirmationMail() : int{

    //$mailbody = 'Bitte öffnen sie folgenden Link um ihre email-Adresse zu bestätigen. \n';

        try {
            // prepare email message
        
            // $message = \Swift_Message::newInstance()
            $message = (new \Swift_Message('Test of Swift Mailer'))
                // ->setSubject('Test of Swift Mailer')
                ->setFrom(['christian.schaefer@vcoe.at' => 'VCÖ - Mobilität mit Zukunft'])
                ->setTo($this->objEmail->get_To())
                ->setSubject($this->objEmail->get_Subject())
                ->setBody($this->objEmail->get_Body())
            ;
            
            // echo $message->toString();
        
            $transport = (new \Swift_SmtpTransport($this->smtp_server, 587, 'tls'))
                            ->setUsername($this->username)
                            ->setPassword($this->password);
            $mailer = (new \Swift_Mailer($transport));
            $result = $mailer->send($message);
        
            if ($result){
                //echo "Number of emails sent:" . $result;
                return $result;
            }else{
                //echo "Could't send email";
                return 0;
            }
        
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

}

?>