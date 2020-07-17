<?php
// echo '<p>TEST CONFIRM</p>';

include_once('myClasses\Vcoeoci.class.php'); 

if(key_exists('hsh', $_GET) and strlen($_GET['hsh']) == 32){
    $vcoe = New myClasses\Vcoeoci;
    $strsql1 = "select * from entries where hashed_email = '" . htmlentities($_GET['hsh']) . "'";
    $arrRecs = $vcoe->ArrayFromDB($strsql1);
}else{
    $arrRecs = [];
};

//Es sollte genau ein Eintrag abgefragt worden sein mit dem entspr. hash...
if(count($arrRecs)>1){

    $strsql2 = "Update entries set marked_del = 0 where hashed_email = '" . htmlentities($_GET['hsh']) . "'";
    $vcoe = New myClasses\Vcoeoci;
    $vcoe->execute($strsql2); 

    if($vcoe->execute($strsql2)>0){
        echo '<p>Vielen Dank! - Ihre Email Adresse wurde bestätigt, Ihr Eintrag erscheint auf unserer Karte.</p>';
        // header("Location: index.php");
        // die();
    }else{
        header("Location: index.php");
        die();
    }

}else{
    echo '<p>Oops! - Hier ist offenbar ein Fehler passiert! Wir konnten Sie nicht identifizieren.</p>';
};

?>
