<?php 

/**
 * Hierher wird das Formular des Popups geschickt!
 */

 include_once('myClasses\Vcoeoci.class.php'); 
 include_once('myClasses\MailTemplate.class.php');
 include_once('myClasses\Mail.class.php');
 include_once('myClasses\Mailer.class.php');
 include_once('myClasses\Entry.class.php');

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

$objEntry = New myClasses\Entry;

/**User-Input is guilty unless the opposite is proven... */

if(key_exists('title', $_POST) && strlen($_POST['title'])>0){
    $objEntry->set_title(htmlentities($_POST['title']));
}else{
    $objEntry->set_title('');
}

if(key_exists('body', $_POST) && strlen($_POST['body'])>0){
    $objEntry->set_description(htmlentities($_POST['body']));
}else{
    $objEntry->set_description('');
}

if(key_exists('notificationtype', $_POST) && strlen($_POST['notificationtype'])>0){
    $objEntry->set_type(htmlentities($_POST['notificationtype']));
}else{
    $objEntry->set_type();
}

if(key_exists('email', $_POST) && strlen($_POST['email'])>0){
    $objEntry->set_email(htmlentities($_POST['email']));
    
}else{
    $objEntry->set_email('');
}

if(key_exists('lat', $_POST) && $_POST['lat'] > 0){
    $objEntry->set_lat($_POST['lat']);
}

if(key_exists('lng', $_POST) && $_POST['lng'] > 0){
    $objEntry->set_lng($_POST['lng']);
}

/**
 * und dann gibt es noch den File-Upload...
 */


if(key_exists("watchthispix", $_FILES) && strlen($_FILES["watchthispix"]["name"]) > 0){ 
    $tmp_name = $_FILES["watchthispix"]["tmp_name"];
    $uploadfilename = $_FILES["watchthispix"]["name"];
    $saveddate = date("mdy-Hms");
    $newfilename = "uploads/".$saveddate."_".$uploadfilename;
    $uploadurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']).'/'.$newfilename;

    //Entry-Objekt soll uploadUrl auch kennen...
    $objEntry->set_uploadUrl($uploadurl);

    if (move_uploaded_file($tmp_name, $newfilename)):
        $msg = "File uploaded";
    else:
        $msg = "Sorry, couldn't upload your picture".$_FILES['watchthispix']['error'];
        $formerrors = true;
    endif; //move uploaded file
}


/**
 * Email erstellen...
 */
$objMailTemplate = New myClasses\MailTemplate($objEntry);

/**
 * Email-Bestätigungs-Email versenden...
 */         
$objMailer = New myClasses\Mailer($objMailTemplate);
//wenn email schon einmal bestätigt wurde, dann muss nicht nocheinmal Bestätigungslink vers. werden...
if($objMailer->objEmail->hasEntries() == false){
    if($objMailer->sendConfirmationMail() > 0){
        //nur dann, wenn das email erfolgreich versandt wurde, wird Eintrag gespeichert...
        if($objEntry->save() == true){
            // echo "Ihr Beitrag wurde gespeichert. Wir haben Ihnen ein Email auf die angegebene Adresse geschickt.";
            $_SESSION['notification'] = "Wir haben Ihnen ein Email geschickt. Bitte bestätigen Sie ihre Email-Adresse indem Sie auf den darin enthaltenen Link klicken, damit wir ihren Beitrag freischalten können.";
            //VCÖ über den Eintrag informieren...
            $objMailer->sendInfoMailToVcoe($objEntry);

            header("Location: index.php");
            die();
        };
    };
}else{
    //Zunächst trotzdem Eintrag speichern!
    $objEntry->save();
    //statt ein Email zu verschicken, sollte an dieser Stelle der Eintrag gleich
    //freigeschalten werden (weil unter der Email-Adresse schon was veröffentlicht wurde!)
    if($objMailer->objEmail->publish() == true){

                $_SESSION['notification'] = "Ihr Eintrag wurde veröffentlicht!";
                //VCÖ über den Eintrag informieren...
                $objMailer->sendInfoMailToVcoe($objEntry);

                header("Location: index.php");
                die();     
    }else{
            header("Location: index.php");
            die();   
    };
};

?>