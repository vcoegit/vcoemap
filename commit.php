<?php 

/**
 * Hierher wird das Formular des Popups geschickt!
 */
require('myClasses\Vcoeoci.class.php');

/**
 * Stimmmt das csrf-Token?
 */
session_start();
 if($_POST['csrf'] !== $_SESSION['csrf_token']) {
    die("Ungültiger Token");
}
   
$echo = '';
$title = '';
$body = '';
$email = '';
$lat = 0;
$lng = 0;

/**User-Input is guily unless the opposite is proven... */

if(key_exists('title', $_POST) && strlen($_POST['title'])>0){
    $title = htmlentities($_POST['title']);
    $echo .= htmlentities($_POST['title']) . '<br />';
}else{
    $echo .= 'no title' . '<br />';
}

if(key_exists('report', $_POST) && strlen($_POST['report'])>0){
    $body = htmlentities($_POST['report']);
    $echo .= htmlentities($_POST['report']) . "<br />";
}else{
    $echo .= 'no report' . '<br />';
}

if(key_exists('email', $_POST) && strlen($_POST['email'])>0){
    $email = htmlentities($_POST['email']);
    $echo .= htmlentities($_POST['email']) . "<br />";
}else{
    $echo .= 'no email' . '<br />';
}

// if(key_exists('lat', $_POST) && $_POST['lat'] > 0){
//     $lat = $_POST['lat'];
// }

// if(key_exists('lng', $_POST) && $_POST['lng'] > 0){
//     $lng = $_POST['lng'];
// }

/**
 * Eingaben in Datenbank speichern!
 */
$vcoe = New myClasses\Vcoeoci;
$query = "insert into entries (title, body, lon, lat, EPSG, email) values ('$title', '$body', '$lng', '$lat', 'EPSG:3857', '$email')"; 

if($vcoe->execute($query)>0){
    // echo "Ihr Beitrag wurde gespeichert!";
    header("Location: index.php");
    die();
};

?>