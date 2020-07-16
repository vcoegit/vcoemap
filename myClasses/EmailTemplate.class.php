<?php 
namespace myClasses;
include 'myClasses/Email.class.php';
use \myClasses\Email;

/**
 * Undocumented class
 */
class EmailTemplate extends Email{

    private $to; //Empfänger-Email-Adresse
    private $body; //Der Body!

    /**
     * Undocumented function
     *
     * @param string $to - Empfänger-Email-Adresse
     * @param string $hashed_email - der email-spezifische Hash-Code zur Identifikation der betr. Person.
     */
    public function __construct(string $to, string $hashed_email){
        
        //auch wenn ev. im Konstruktor von Email nix passiert... (vielleicht ja später...)
        parent::__construct();

        $link = $_SERVER('HOST_NAME') . "/leaflet2020/$hashed_email";

        $this->set_body = '<p>Ihre email-Adresse wurde in eines unserer Formulare auf ' . $_SERVER('HOST_NAME'). ' eingegeben.</p><br />' . 
        '<p>Um die Richtigkeit dieser Angabe zu bestätigen, klicken Sie bitte folgenden Link an:</p><br >' . $link . '<p>Oder kopieren Sie den Link in die Adresszeile ihres Browsers.</p><br /><br />' . 
        '<p>Ihr Beitrag erscheint anschließend auf unserer Karte!</p>' . '<br />' .
        '<p>Vielen Dank für Ihren Beitrag!</p>';

        $this->set_to = $to;

        $this->set_from = ['christian.schaeferr@vcoe.at' => 'VCÖ - Mobilität mit Zukunft'];

        $this->set_subject = 'Bestätigungslink VCÖ-Karten-Tool';

    }

};

?>