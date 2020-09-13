<?php namespace myClasses;

use \PDO;
/**
 * Klasse, verwaltet die Verbindung zur Datenbank. Get Instance gibt eine Datenbankverbindung zurück, wenn noch keine existiert
 * wird eine erstellt.
 */
class ConnectionMysql{

public static $conn = null;
public static $instance = null;
private $configs; //Array

//der Constructor wird protected gesetzt, d.h. von ausserhalb des Objekts kann keine Instanz mehr erstellt werden...
/**
 * Konstruktor der Connection-Klasse
 */
protected function __construct(){
    
    $this->configs = include('config.php');

    if($this->configs['env']=='dev'){
        $dsn = $this->configs['env_dev']['dsn'];
        $user = $this->configs['env_dev']['user'];
        $password = $this->configs['env_dev']['password'];
    }else{
        $dsn = $this->configs['env_prod']['dsn'];
        $user = $this->configs['env_prod']['user'];
        $password = $this->configs['env_prod']['password'];
    }


    try {
        self::$conn = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        // echo 'Fehler! - Datenbankverbindung konnte nicht hergestellt werden '; 
        // echo "Fehler",  $e->getMessage();
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
        self::$instance = new ConnectionMysql();
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