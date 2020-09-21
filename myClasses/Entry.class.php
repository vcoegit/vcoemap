<?php 
namespace myClasses;

include_once 'myClasses/Vcoeoci.class.php'; 

class Entry{

    private $entryid;
    private $vorname;
    private $nachname;
    private $email;
    private $hashedEmail;
    private $title;
    private $plz;
    private $description;
    private $type; //notification_type
    private $lat;
    private $lng;
    private $uploadUrl;
    private $linkConfirm;
    private $linkDelete;
    private $toc;
    private $configs;

    //Betrifft die Verortung des Eintrags...
    private $gemeinde; //gb
    private $bundesland; //bl
    private $staat; //st

    public function __construct(){

        $this->configs = include('config.php');

    }

    public function get_entryid(){
        if(!$this->entryid){
            return null;
        }else{
            return $this->entryid;
        }
    }

    public function set_entryid(){

    }

    private function set_linkDelete(){

        if($this->configs['env']=='dev'){
            $this->linkDelete = 'http://' . $_SERVER['SERVER_NAME'] . '/leaflet2020/block.php?hsh=' . $this->get_hashedEmail() . '&entryid=' . $this->get_entryid();
        }else{
            $this->linkDelete = 'http://' . $_SERVER['SERVER_NAME'] . '/block.php?hsh=' . $this->get_hashedEmail() . '&entryid=' . $this->get_entryid();    
        }

    }

    public function get_linkDelete(){
        return $this->linkDelete;
    } 

    public function set_vorname(string $vorname) : Entry {
        $this->vorname = $vorname;
        return $this;
    }

    public function get_vorname(){
        return $this->vorname;
    }

    public function set_nachname(string $nachname) : Entry {
        $this->nachname = $nachname;
        return $this;
    }

    public function get_nachname(){
        return $this->nachname;
    }

    public function set_email(string $email) : Entry {
        $this->email = $email;
        $this->set_hashedEmail($email)->set_linkDelete();
        return $this;
    }

    public function get_email(){
        return $this->email;
    }

    public function get_title(){
        return $this->title;
    }

    public function set_title(string $title) : Entry {
        $this->title = $title;
        return $this;
    }

    public function get_plz(){
        return $this->plz;
    }

    public function set_plz(string $plz) : Entry {
        $this->plz = $plz;
        return $this;
    }

    private function set_hashedEmail(string $email) : Entry {
        $this->hashedEmail = hash('md4', $email . 'salt&pepper');
        return $this;
    }

    public function get_hashedEmail(){
        return $this->hashedEmail;
    }

    
    public function set_description(string $description) : Entry {
        $this->description = $description;
        return $this;
    }

    public function get_description(){
        return $this->description;
    }

    public function set_type(string $type) : Entry {
        $this->type = $type;
        return $this;
    }

    public function get_type(){
        return $this->type;
    }

    public function set_lat($lat) : Entry{
        $this->lat = $lat;
        return $this;
    }

    public function get_lat(){
        return $this->lat;
    }

    public function set_lng($lng) : Entry{
        $this->lng = $lng;
        return $this;
    }

    public function get_lng(){
        return $this->lng;
    }

    public function set_uploadUrl(String $url) : Entry{
        $this->uploadUrl = $url;
        return $this;
    }
    
    public function get_uploadUrl(){
        return $this->uploadUrl;
    }

    public function set_gemeinde(String $gemeinde) : Entry{
        $this->gemeinde = $gemeinde;
        return $this;
    }

    public function get_gemeinde(){
        return $this->gemeinde;
    }

    public function set_bundesland(String $bundesland) : Entry{
        $this->bundesland = $bundesland;
        return $this;
    }

    public function get_bundesland(){
        return $this->bundesland;
    }

    public function set_staat(String $staat) : Entry{
        $this->staat = $staat;
    }

    public function get_staat(){
        return $this->staat;
    }

    //toc - Terms of conditions
    public function set_toc(String $truefalse) : Entry{
        $this->toc = $truefalse;
        return $this;
    }

    public function get_toc(){
        return $this->toc;
    }

    public function save() : bool {
        $vcoe = New \myClasses\Vcoeoci;
        $query = "insert into entries (title, body, lon, lat, EPSG, email, filepath, notification_type, hashed_email, plz, terms_of_use, gemeinde, bezirk, bundesland, vorname, nachname) values ('" . $this->get_title() . "', '" . $this->get_description() . "', '" .$this->get_lng() . "', '" . $this->get_lat() . "', 'EPSG:3857', '" . $this->get_email() . "', '" .$this->get_uploadurl() . "', '" . $this->get_type() . "', '"  . $this->get_hashedEmail() . "' , '"  . $this->get_plz() . "' , '" . $this->get_toc() . "' , '" . $this->get_gemeinde() . "', '" . $this->get_bezirk() . "', '" . $this->get_bundesland() . "', '" . $this->get_vorname() . "', '" . $this->get_nachname() . "')"; 

        if($vcoe->execute($query)>0){
            // $this->entryid = SELECT LAST_INSERT_ID();

            $vcoe = New \myClasses\Vcoeoci;
            $strsql = "SELECT MAX(entryid) from entries";
            $strsql = "SELECT LAST_INSERT_ID()";
            
            $entryid = $vcoe->scalarFromDB($strsql);
            $this->entryid = $vcoe->ScalarFromDB($strsql);

            //erst jetzt kann ich einen Lösch-Link erzeugen, da dieser die Entryid enthalten soll...
            $this->set_linkDelete();

            return true;
        }else{

            return false;
        }
    }

}

?>