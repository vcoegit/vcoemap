<?php 
namespace myClasses;
include 'myClasses/Mail.class.php';

/**
 * Undocumented class
 */
class MailTemplate extends Mail{
    /**
     * Undocumented function
     *
     * @param string $to - Empfänger-Email-Adresse
     * @param string $hashed_email - der email-spezifische Hash-Code zur Identifikation der betr. Person.
     */

    public $link;
    public $image;
    public $html;
    private $configs;

    public function __construct(Entry $objEntry){
        
        $this->configs = include('config.php');

        //auch wenn ev. im Konstruktor von Email nix passiert... (vielleicht ja später...)
        parent::__construct();

        //$link = $_SERVER('HOST_NAME') . "/leaflet2020/$hashed_email";
        
        if($this->configs['env']=='dev'){
            $this->link = 'http://' . $_SERVER['SERVER_NAME'] . '/leaflet2020/confirm.php?hsh=' . 
            $objEntry->get_hashedEmail();
        }else{
            $this->link = 'http://' . $_SERVER['SERVER_NAME'] . '/confirm.php?hsh=' . 
            $objEntry->get_hashedEmail();        
        }


        $this->set_bodyText('Ihre email-Adresse wurde in eines unserer Formulare auf ' . $_SERVER['SERVER_NAME'] . ' eingegeben.' . "\n" . 
        'Um die Richtigkeit dieser Angabe zu bestätigen, klicken Sie bitte folgenden Link an:' . "\n" . $this->link . "\n" . 'Oder kopieren Sie den Link in die Adresszeile ihres Browsers.' . 
        'Ihr Beitrag erscheint anschließend auf unserer Karte!' . "\n" .
        'Vielen Dank für Ihren Beitrag!');

        $this->set_to($objEntry->get_Email());

        $this->set_from('christian.schaefer@vcoe.at');

        $this->set_subject('Bestätigungslink problemstellen.vcoe.at');
        
    }
}
?>
