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

if(key_exists('centerLng', $_POST) && $_POST['centerLng'] > 0){
    $_SESSION['centerLng'] = $_POST['centerLng'];
    // $center = $_POST['center']; 
}

if(key_exists('centerLat', $_POST) && $_POST['centerLat'] > 0){
    $_SESSION['centerLat'] = $_POST['centerLat'];
    // $center = $_POST['center'];
}

if(key_exists('zoom', $_POST) && $_POST['zoom'] > 0){
    $_SESSION['zoom'] = $_POST['zoom'];
    // $zoom = $_POST['zoom'];
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

if(key_exists('lat', $_POST) && $_POST['lat'] > 0){
    $lat = $_POST['lat'];
}

if(key_exists('lng', $_POST) && $_POST['lng'] > 0){
    $lng = $_POST['lng'];
}

/**
 * und dann gibt es noch den File-Upload...
 */
$tmp_name = $_FILES["watchthispix"]["tmp_name"];
$uploadfilename = $_FILES["watchthispix"]["name"];
$saveddate = date("mdy-Hms");
$newfilename = "uploads/".$saveddate."_".$uploadfilename;
$uploadurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']).'/'.$newfilename;

if (move_uploaded_file($tmp_name, $newfilename)):
    $msg = "File uploaded";
else:
    $msg = "Sorry, couldn't upload your profile picture".$_FILES['file']['error'];
    $formerrors = true;
endif; //move uploaded file

/**
 * Eingaben in Datenbank speichern!
 */
$vcoe = New myClasses\Vcoeoci;
$query = "insert into entries (title, body, lon, lat, EPSG, email, filepath) values ('$title', '$body', '$lng', '$lat', 'EPSG:3857', '$email', '$uploadurl')"; 


if($vcoe->execute($query)>0){
    // echo "Ihr Beitrag wurde gespeichert!";
    header("Location: index.php");
    die();
};

?>