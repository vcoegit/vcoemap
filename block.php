<?php

// echo '<p>TEST BLOCK</p>';

include_once('myClasses/Vcoeoci.class.php'); 

if(key_exists('hsh', $_GET) and strlen($_GET['hsh']) == 32){
    $vcoe = New myClasses\Vcoeoci;
    $strsql1 = "select * from entries where hashed_email = '" . htmlentities($_GET['hsh']) . "'";
    $arrRecs = $vcoe->EntriesArrayFromDB($strsql1);
}else{
    $arrRecs = [];
};

//Es sollte genau ein Eintrag abgefragt worden sein mit dem entspr. hash...
if(count($arrRecs)>0){

    $strsql2 = "Update entries set marked_del = 1 where hashed_email = '" . htmlentities($_GET['hsh']) . "' and entryid = " . htmlentities($_GET['entryid']);
    $vcoe = New myClasses\Vcoeoci;
    $vcoe->execute($strsql2); 

    header("Location: index.php");
    die();

};


?>