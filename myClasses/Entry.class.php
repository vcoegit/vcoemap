<?php 
namespace myClasses;

include_once 'myClasses\Vcoeoci.class.php'; 

class Entry{

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


    public function __construct(){

    }

    private function set_linkDelete(){
        $this->linkDelete = 'http://' . $_SERVER['SERVER_NAME'] . '/leaflet2020/block.php?hsh=' . $this->get_hashedEmail();
    }

    public function get_linkDelete(){
        return $this->linkDelete;
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

    public function save() : bool {
        $vcoe = New \myClasses\Vcoeoci;
        $query = "insert into entries (title, body, lon, lat, EPSG, email, filepath, notification_type, hashed_email, plz) values ('" . $this->get_title() . "', '" . $this->get_description() . "', '" .$this->get_lng() . "', '" . $this->get_lat() . "', 'EPSG:3857', '" . $this->get_email() . "', '" .$this->get_uploadurl() . "', '" . $this->get_type() . "', '"  . $this->get_hashedEmail() . "' , '"  . $this->get_plz() . "')"; 

        if($vcoe->execute($query)>0){
            return true;
        }else{
            return false;
        }
    }

}

?>