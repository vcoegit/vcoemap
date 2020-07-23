<?php 
namespace myClasses;

include_once 'myClasses\Vcoeoci.class.php'; 

class Mail{

    private $to; //email
    private $from; //email
    private $subject;
    private $bodyText;
    private $bodyHTML;
    private $cc; //array?
    private $bcc; //array?
    private $strSQL; 
    private $strSQL2;

    public function __construct(){

    }

    public function set_to(string $to) : Mail{
        $this->to = $to;
        return $this;
    }

    public function get_to(){
        return $this->to;
    }

    public function set_from(string $from) : Mail{
        $this->from = $from;
        return $this;
    }

    public function get_from(){
        return $this->from;
    }

    public function set_subject(string $subject) : Mail{
        $this->subject = $subject;
        return $this;
    }

    public function get_subject(){
        return $this->subject;
    }

    public function set_bodyText(string $text) : Mail{
        $this->bodyText = $text;
        return $this;
    }


    public function get_bodyText(){
        return $this->bodyText;
    }

    public function set_bodyHTML(string $html) : Mail{
        $this->bodyHTML = $html;
        return $this;
    }

    public function get_bodyHTML(){
        return $this->bodyHTML;
    }

    public function add_cc(string $cc) : Mail{
        $this->cc[] = $cc;
        return $this;
    }

    public function remove_cc(string $cc){
        //CODE HERE
    }

    public function add_bcc(string $bcc) : Mail{
        $this->bcc[] = $bcc;
        return $this;
    }

    public function remove_bcc(){
        //CODE HERE
    }

    /**
     * Prüft ob es zur jew. to-Adresse schon Datenbankeinträge gibt...
     *
     * @return boolean
     */
    public function hasEntries() : bool{

    $vcoe = New \myClasses\Vcoeoci;

    //gibt es bereits freigeschaltete Einträge?
    $this->strSQL = "SELECT * from entries where email = '" . $this->get_to() . "' and marked_del = 0";
        if(count($vcoe->ArrayFromDB($this->strSQL))>0){
            return true;
        }else{
            return false;
        }
    }

    //Eintrag wird / Einträge werden veröffentlicht! (alle die zu dieser Email gehören!)
    public function publish() : bool{

    $vcoe = New \myClasses\Vcoeoci;

    $this->strSQL2 = "update entries set marked_del = 0 where email = '" . $this->get_to() . "'";

        $vcoe->execute($this->strSQL2);
        return true;
        
    }

};