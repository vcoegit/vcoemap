<?php 
namespace myClasses;

include_once 'myClasses/Vcoeoci.class.php'; 

class Hit{

    //Betrifft die Verortung des Eintrags...
    private $gemeinde; //gb
    private $bundesland; //bl
    private $staat; //st

    private $lat;
    private $lng;
    private $latlng;
    private $lnglat;

    public function __construct(){

    }

    public function set_lat($lat) : Hit {
        $this->lat = $lat;
        //Wann immer der lat-Wert geändert wird, ändert sich auch der $latlng-Wert...
        // $this->set_latlng($this->lat . ',' . $this->lng);
        $this->set_lnglat($this->lng . ',' . $this->lat);
        return $this;
    }

    public function get_lat(){
        return $this->lat;
    }

    public function set_lng($lng) : Hit {
        $this->lng = $lng;
        //Wann immer der lng-Wert geändert wird, ändert sich auch der $latlng-Wert...
        // $this->set_latlng($this->lat . ',' . $this->lng);
        $this->set_lnglat($this->lng . ',' . $this->lat);
        return $this;
    }

    public function get_lng(){
        return $this->lng;
    }

    private function set_latlng(String $latlng){
        $this->latlng = $latlng;

        //Wann immer sich die Koordinaten ändern, müssen sie Ortsbezeichnungen ermittelt werden...
        $this->query_location_names();

    }

    public function get_latlng(){
        //...in dieser Form: '14.798583984375002, 47.908978314728714'
        return $this->latlng;
    }

    private function set_lnglat(String $lnglat){
        $this->lnglat = $lnglat;

        //Wann immer sich die Koordinaten ändern, müssen sie Ortsbezeichnungen ermittelt werden...
        $this->query_location_names();

    }

    public function get_lnglat(){
        //...in dieser Form: '14.798583984375002, 47.908978314728714'
        return $this->lnglat;
    }

    // public function set_gemeinde(String $gemeinde) : Hit{
    //     $this->gemeinde = $gemeinde;
    // }

    public function get_gemeinde(){
        return $this->gemeinde;
    }

    // public function set_bundesland(String $bundesland) : Hit{
    //     $this->bundesland = $bundesland;
    // }

    public function get_bundesland(){
        return $this->bundesland;
    }

    // public function set_staat(String $staat) : Hit{
    //     $this->staat = $staat;
    // }

    public function get_staat(){
        return $this->staat;
    }

    private function query_location_names(){
        //..also Gemeinde, Bundesland, Staat...
        
        if(isset($this->lat) && isset($this->lng)){
            $vcoe = New \myClasses\Vcoeoci;
            $strSQL = "select gb, bl, st from borders where ST_contains(shape, point(" . $this->lnglat . "))";
            $arr = $vcoe->BordersArrayFromDB($strSQL);
            $this->gemeinde = $arr[0][0];
            $this->bundesland = $arr[0][1];
            $this->staat = $arr[0][2];
        }
    }

}