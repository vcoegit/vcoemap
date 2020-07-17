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
    public function __construct(string $to, string $hashed_email){
        
        //auch wenn ev. im Konstruktor von Email nix passiert... (vielleicht ja später...)
        parent::__construct();

        //$link = $_SERVER('HOST_NAME') . "/leaflet2020/$hashed_email";
        $link = 'http://' . $_SERVER['SERVER_NAME'] . '/leaflet2020/confirm.php?hsh=' . $hashed_email;

        $this->set_body('Ihre email-Adresse wurde in eines unserer Formulare auf ' . $_SERVER['SERVER_NAME'] . ' eingegeben.' . "\n" . 
        'Um die Richtigkeit dieser Angabe zu bestätigen, klicken Sie bitte folgenden Link an:' . "\n" . $link . "\n" . 'Oder kopieren Sie den Link in die Adresszeile ihres Browsers.' . 
        'Ihr Beitrag erscheint anschließend auf unserer Karte!' . "\n" .
        'Vielen Dank für Ihren Beitrag!');

        $this->set_to($to);

        $this->set_from('christian.schaefer@vcoe.at');

        $this->set_subject('Bestätigungslink VCÖ-Karten-Tool');

    }

};

?>