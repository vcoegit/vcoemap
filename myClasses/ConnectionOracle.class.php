<?php namespace myClasses;

/**
 * Klasse, verwaltet die Verbindung zur Datenbank. Get Instance gibt eine Datenbankverbindung zurück, wenn noch keine existiert
 * wird eine erstellt.
 */
class Connection{

public static $conn = null;
public static $instance = null;

//der Constructor wird protected gesetzt, d.h. von ausserhalb des Objekts kann keine Instanz mehr erstellt werden...
/**
 * Konstruktor der Connection-Klasse
 */
protected function __construct(){
    try{
        //$this->conn = oci_connect('vcoe', 'vcoe', '192.168.1.29:1523/vcoe2014.vcoe.local');
        self::$conn = oci_connect('vcoe', 'vcoe', '192.168.1.29:1523/vcoe2014.vcoe.local', 'AL32UTF8');
    }
    catch(Exception $e)
    {
        echo 'Fehler! - Datenbankverbindung konnte nicht hergestellt werden '; 
        echo "Fehler",  $e->getMessage();
    }
}

/**
 * Die statische Methode liefert eine Instanz der im Konstruktor definierten Datenbankverbindung zurück 
 * (Will man eine Instanz der Datenbankverbindung erzeugen, ist man auf die Methode getInstance angewiesen)
 *
 * @return void
 */
public static function getInstance(){
    if(self::$instance == null){
        self::$instance = new Connection();
    }
    return self::$conn;
}

/**
 * das Clonen des Objekts außerhalb der Klasse wird unterbunden, durch Überschreiben der clone-Methode 
 * wobei diese auf private gesetzt wird! (verhindert, dass das Singleton-Prinzip umgangen werden kann)
 *
 * @return void
 */
private function __clone(){

}

}

?>