<?php namespace myClasses;

require_once 'vendor/autoload.php';
require_once 'myClasses/Entry.class.php';

class Mailer{

    private $smtp_server;
    private $username;
    private $password;
    public $objEmail;
    private $configs; //Array


    public function __construct(Mail $objEmail){
        /**
         * Das könnte man auch in ein Konfigurationsfile auslagern...
         */

        $this->configs = include('config.php');

        $this->objEmail = $objEmail;

        if($this->configs['env']=='dev'){
            $this->smtp_server = $this->configs['env_dev']['mailaccount']['smtp_server'];
            $this->username = $this->configs['env_dev']['mailaccount']['username'];
            $this->password = $this->configs['env_dev']['mailaccount']['password'];
        }else{
            $this->smtp_server = $this->configs['env_prod']['mailaccount']['smtp_server'];
            $this->username = $this->configs['env_prod']['mailaccount']['username'];
            $this->password = $this->configs['env_prod']['mailaccount']['password'];         
        }

    }
    
    public function sendConfirmationMail() : int{

    //$mailbody = 'Bitte öffnen sie folgenden Link um ihre email-Adresse zu bestätigen. \n';

    if($this->configs['env']=='dev'){
        $email_from = $this->configs['env_dev']['mailaccount']['email_from_email'];
        $name_from = $this->configs['env_dev']['mailaccount']['email_from_name'];
    }else{
        $email_from = $this->configs['env_prod']['mailaccount']['email_from_email'];
        $name_from = $this->configs['env_prod']['mailaccount']['email_from_name'];
    }

        try {
            // prepare email message
            $message = (new \Swift_Message('Bestätigungslink'))
                ->setFrom([$email_from => $name_from])
                ->setTo($this->objEmail->get_To())
                ->setSubject($this->objEmail->get_Subject());

                $image = $message->embed((new \Swift_Image())->fromPath('images/vcoe_logo_newsletter.png'));
                $link = $this->objEmail->link;

//auch wenn das mein ganzes Konzept über den Haufen wirft...(das wollte ich an dieser Stelle nicht haben...)
                $html = <<<HEREDOC
                <!DOCTYPE HTML><html lang="de"><head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="Content-Language" content="de-at">
                
                <meta http-equiv="expires" content="0">
                <meta http-equiv="Content-Style-Type" content="text/css">
                <meta http-equiv="x-ua-compatible" content="IE=edge">
                
                <style>
                body {
                width: 100%;
                font-family: "Verdana", sans-serif;
                background-color: #ededed;
                margin: 0;
                padding: 0;
                }
                
                a, a:link, a:visited {
                color: #3188b6;
                text-decoration: underline;
                }
                
                @media only screen and (max-width: 500px) {
                .fullWidth {
                display: block !important;
                width: auto !important;
                float: none;
                }
                
                .fullWidthImg {
                width: 100% !important;
                float: none;
                text-align: center;
                }
                
                .fullWidthImg img {
                width: auto !important;
                }
                
                .logo {
                width: 100%;
                }
                }
                
                @media only screen and (max-width: 820px) {
                #wrapper {
                width: 100% !important;
                }
                }
                
                .fullWidth a {
                color: #3188b6 !important;
                }
                
                .footer a {
                color: #ffffff !important;
                }
                </style>
                <title>Newsletter</title>
                </head>
                <body>
                
                <table bgcolor="#ededed" border="0" cellpadding="0" cellspacing="0" style="background: #ededed;" width="100%">
                <tbody>
                <tr>
                <td style="font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" id="wrapper" width="700">
                <tbody>
                <tr>
                <td align="left" class="left" style="font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; padding: 12px 15px; background: #3188b6;" valign="middle"><img alt="VCÖ-Mobilität mit Zukunft" id="logo" src="$image" style="max-width: 243px; width: 100%; min-width: 150px;" width="243"></td>
                </tr>
                
                <tr>
                <td bgcolor="#ffffff" style="width: 100% !important; font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; background: #ffffff;" valign="top" width="100%">
                <table align="center" bgcolor="white" border="0" cellpadding="0" cellspacing="0" class="main" style="background: white; padding-bottom: 24px;" width="100%">
                <tbody>
                <tr>
                <td align="left" class="welcome" style="color: #000000; font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; padding: 12px 15px;" valign="middle">
                <h1 style="font-size: 21px; font-weight: normal; color: #3188b6; margin: 12px 0;">
                Nur noch ein Klick und Ihr Beitrag ist veröffentlicht!</h1>
                
                <p>Danke für Ihren Beitrag zum VCÖ-Check &quot;Hindernisse für bewegungsaktive Mobilität&quot;.</p>
                
                <p>Bitte bestätigen Sie mit einem Klick auf den folgenden Link Ihre E-Mail-Adresse</p>
                
                <p><a href="$link" title="Problemstellen - Email Bestätigung">$link</a></p>
                
                <p>Ihr Beitrag wird gleich anschließend veröffentlicht und ist für alle anderen sichtbar.</p>
                
                <p>Der VCÖ prüft alle neuen Einträge in regelmäßigen Abständen und löscht Einträge, die Anstandsregeln verletzen oder keinen sinnvollen Beitrag leisten.</p>
                
                <p>Falls Sie Ihrem Beitrag&nbsp;ein Foto hinzugefügt haben, erklären Sie mit der Bestätigung Ihrer E-Mail-Adresse, dass Sie damit keine Urheber- oder Persönlichkeitsrechte verletzen.</p>
                
                <p>Für Fragen zu Ihrem Eintrag und zum VCÖ-Check allgemein steht Ihnen das VCÖ-Team unter vcoe@vcoe.at gerne zur Verfügung.</p>
                
                <p>Herzlichen Dank für Ihren Beitrag und herzliche Grüße!</p>
                
                <p>Christoph Hörhan<br>
                Für das VCÖ-Team</p>
                
                <p>VCÖ - Mobilität mit Zukunft<br>
                Bräuhausgasse 7-9<br>
                1050 Wien<br>
                ZVR-Zahl: 674059554<br>
                Tel.: &#43;43-(0)1-893 26 97<br>
                E-Mail: vcoe@vcoe.at<br>
                www.vcoe.at</p>
                
                <p>Unter www.vcoe.at/zusendungen erfahren Sie, warum Sie diese Nachricht erreicht hat. Die ausführliche Datenschutzerklärung des VCÖ finden Sie auf www.vcoe.at/datenschutz.</p>
                </td>
                </tr>

                <tr>
                <td>
                <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0 width="100%" style='width:100.0%;background:#FECB13'><tr><td width=75 style='width:56.25pt;padding:4.5pt 9.0pt 4.5pt 9.0pt'><p class=MsoNormal style='line-height:18.0pt'><span style='font-size:16.0pt;font-family:"Verdana",sans-serif'><span style='color:white'><img border=0 id="_x0000_i1026" src="http://mailer.marmara.co.at/att/vcoe/arrow.jpg" alt=VCÖ></span><o:p></o:p></span></p></td><td style='padding:4.5pt 9.0pt 4.5pt 9.0pt'><p class=MsoNormal style='line-height:18.0pt'><span style='font-size:16.0pt;font-family:"Verdana",sans-serif'><a href="http://www.vcoe.at/spende" style='text-decoration:none'><span style='text-decoration:none'>Bitte unterstützen Sie den VCÖ bei seinem Einsatz für Bewegungsaktive Mobilität mit Ihrer Spende </span></a><o:p></o:p></span></p></td></tr></table>
                </td>
                </tr>

                </tbody>
                </table>
                </td>
                </tr>
                <tr>
                <td bgcolor="#3188b6" style="font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; background: #3188b6;" valign="top" width="100%">                

                </td>
                </tr>
                
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </body>
                </html>
HEREDOC;

                $message->setBody($html, 'text/html')
                ->addPart($this->objEmail->get_BodyText(), 'text/plain')
            ;
            
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

    /**
     * Die Methode ist für das Versenden der Info-Mail an den VCÖ verantwortlich.
     *
     * @return integer
     */
    public function sendInfoMailToVcoe(Entry $objEntry) : int {

        $uploadurl = $objEntry->get_uploadUrl();
        $email = $objEntry->get_email();
        $plz = $objEntry->get_plz();
        $title = $objEntry->get_title();
        $description = $objEntry->get_description();
        $type = $objEntry->get_type();
        $lat = $objEntry->get_lat();
        $lng = $objEntry->get_lng();
        $linkDelete = $objEntry->get_linkDelete();

        if($this->configs['env']=='dev'){
            $email_from = $this->configs['env_dev']['mailaccount']['email_from_email'];
            $name_from = $this->configs['env_dev']['mailaccount']['email_from_name'];
            $email_to = $this->configs['env_dev']['mailaccount']['email_to_email'];
        }else{
            $email_from = $this->configs['env_prod']['mailaccount']['email_from_email'];
            $name_from = $this->configs['env_prod']['mailaccount']['email_from_name'];
            $email_to = $this->configs['env_prod']['mailaccount']['email_to_email'];
        }

        try {
            // prepare email message
            $message = (new \Swift_Message('Bestätigungslink'))
                // ->setSubject('Test of Swift Mailer')
                ->setFrom([$email_from => $name_from])
                ->setTo([$email_to])
                ->setSubject('Neuer Eintag in VCOE-Kartentool - please check!');

                $logo = $message->embed((new \Swift_Image())->fromPath('images/vcoe_logo_newsletter.png'));
                
                if(strlen($uploadurl) > 0){
                    $image = $message->embed((new \Swift_Image())->fromPath($uploadurl));
                }else{
                    $image = $message->embed((new \Swift_Image())->fromPath('images/vcoe_logo_newsletter.png'));
                }

                $html = <<<HEREDOC
                <!DOCTYPE HTML><html lang="de"><head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="Content-Language" content="de-at">
                
                <meta http-equiv="expires" content="0">
                <meta http-equiv="Content-Style-Type" content="text/css">
                <meta http-equiv="x-ua-compatible" content="IE=edge">
                
                <style>
                body {
                width: 100%;
                font-family: "Verdana", sans-serif;
                background-color: #ededed;
                margin: 0;
                padding: 0;
                }
                
                a, a:link, a:visited {
                color: #3188b6;
                text-decoration: underline;
                }
                
                @media only screen and (max-width: 500px) {
                .fullWidth {
                display: block !important;
                width: auto !important;
                float: none;
                }
                
                .fullWidthImg {
                width: 100% !important;
                float: none;
                text-align: center;
                }
                
                .fullWidthImg img {
                width: auto !important;
                }
                
                .logo {
                width: 100%;
                }
                }
                
                @media only screen and (max-width: 820px) {
                #wrapper {
                width: 100% !important;
                }
                }
                
                .fullWidth a {
                color: #3188b6 !important;
                }
                
                .footer a {
                color: #ffffff !important;
                }
                </style>
                <title>Newsletter</title>
                </head>
                <body>
                
                <table bgcolor="#ededed" border="0" cellpadding="0" cellspacing="0" style="background: #ededed;" width="100%">
                <tbody>
                <tr>
                <td style="font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" id="wrapper" width="700">
                <tbody>
                <tr>
                <td align="left" class="left" style="font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; padding: 12px 15px; background: #3188b6;" valign="middle"><img alt="VCÖ-Mobilität mit Zukunft" id="logo" src="$logo" style="max-width: 243px; width: 100%; min-width: 150px;" width="243"></td>
                </tr>
                
                <tr>
                <td bgcolor="#ffffff" style="width: 100% !important; font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; background: #ffffff;" valign="top" width="100%">
                <table align="center" bgcolor="white" border="0" cellpadding="0" cellspacing="0" class="main" style="background: white; padding-bottom: 24px;" width="100%">
                <tbody>
                <tr>
                <td align="left" class="welcome" style="color: #000000; font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; padding: 12px 15px;" valign="middle">
                <h1 style="font-size: 21px; font-weight: normal; color: #3188b6; margin: 12px 0;">
                Neuer Beitrag VCÖ-Map!</h1>
                
                <p>Ein Benutzer hat soeben einen Betrag hochgeladen!</p>
                
                <p>Bitte prüfen.</p>
                
                <p>File(Image)-Upload-Url: <a>$uploadurl</a></p>
                <p>Email: $email</p>
                <p>Title: $title</p>
                <p>Beschreibung: $description</a></p>
                <p>Folgendes Bild wurde hochgeladen: </p>
                <img alt="VCÖ-Mobilität mit Zukunft" id="logo" src="$image" style="max-width: 243px; width: 100%; min-width: 150px;" width="243">
        
                <p>Beitrag wurde vermutlich bereits veröffentlicht und ist für alle anderen sichtbar.</p>
                <p>Um diesen Beitrag als gelöscht zu markieren, betätigen Sie bitte unten stehenden Link:</p>
                <p>Damit werden alle Beiträge unsichtbar, die unter dieser Email-Adresse veröffentlicht wurden</p>

                <a href="$linkDelete">$linkDelete</a>

                <tr>
                <td bgcolor="#3188b6" style="font-family: Verdana, sans-serif; font-size: 14px; line-height: 24px; background: #3188b6;" valign="top" width="100%">                
                
                </td>
                </tr>
                
                </tbody>
                </table>
                </td>
                </tr>
                </tbody>
                </table>
                </body>
                </html>
HEREDOC;

                $message->setBody($html, 'text/html')
                ->addPart('', 'text/plain');
            
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