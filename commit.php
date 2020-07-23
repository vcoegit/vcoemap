<?php 

/**
 * Hierher wird das Formular des Popups geschickt!
 */

 include_once('myClasses\Vcoeoci.class.php'); 
 include_once('myClasses\MailTemplate.class.php');
 include_once('myClasses\Mail.class.php');
 include_once('myClasses\Mailer.class.php');

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

if(key_exists('body', $_POST) && strlen($_POST['body'])>0){
    $body = htmlentities($_POST['body']);
    $echo .= htmlentities($_POST['body']) . "<br />";
}else{
    $echo .= 'no body' . '<br />';
}

if(key_exists('notificationtype', $_POST) && strlen($_POST['notificationtype'])>0){
    $notificationtype = htmlentities($_POST['notificationtype']);
    $echo .= htmlentities($_POST['notificationtype']) . "<br />";
}else{
    $echo .= 'no body' . '<br />';
}

if(key_exists('email', $_POST) && strlen($_POST['email'])>0){
    $email = htmlentities($_POST['email']);
    $hashed_email = hash('md4', $email . 'salt&pepper');
    $echo .= htmlentities($_POST['email']) . "<br />";
}else{
    $echo .= 'no email' . '<br />';
    $hashed_email = '';
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


if(key_exists("watchthispix", $_FILES)){ 
    $tmp_name = $_FILES["watchthispix"]["tmp_name"];
    $uploadfilename = $_FILES["watchthispix"]["name"];
    $saveddate = date("mdy-Hms");
    $newfilename = "uploads/".$saveddate."_".$uploadfilename;
    $uploadurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']).'/'.$newfilename;

    if (move_uploaded_file($tmp_name, $newfilename)):
        $msg = "File uploaded";
    else:
        $msg = "Sorry, couldn't upload your picture".$_FILES['watchthispix']['error'];
        $formerrors = true;
    endif; //move uploaded file
}




/**
 * Eingaben in Datenbank speichern!
 */
$vcoe = New myClasses\Vcoeoci;
$query = "insert into entries (title, body, lon, lat, EPSG, email, filepath, notification_type, hashed_email) values ('$title', '$body', '$lng', '$lat', 'EPSG:3857', '$email', '$uploadurl', '$notificationtype', '$hashed_email')"; 


/**
 * Email erstellen...
 */
$objMailTemplate = New myClasses\MailTemplate($email, $hashed_email);

/**
 * Email-Bestätigungs-Email versenden...
 */         
$objMailer = New myClasses\Mailer($objMailTemplate);
//wenn email schon einmal bestätigt wurde, dann muss nicht nocheinmal Bestätigungslink vers. werden...
if($objMailer->objEmail->hasEntries() == false){
    if($objMailer->sendConfirmationMail() > 0){
        // var_dump('yepp!');
        //nur dann, wenn das email erfolgreich versandt wurde, wird Eintrag gespeichert...
        if($vcoe->execute($query)>0){
            // echo "Ihr Beitrag wurde gespeichert. Wir haben Ihnen ein Email auf die angegebene Adresse geschickt.";
            header("Location: index.php");
            die();
        };
    };
}else{
    //Zunächst trotzdem Eintrag speichern!
    $vcoe->execute($query);
    //statt ein Email zu verschicken, sollte an dieser Stelle der Eintrag gleich
    //freigeschalten werden (weil unter der Email-Adresse schon was veröffentlicht wurde!)
    if($objMailer->objEmail->publish() == true){
            // echo "Ihr Beitrag wurde veröffentlicht!";
                header("Location: index.php");
                die();     
    }else{
            header("Location: index.php");
            die();   
    };
};


?>