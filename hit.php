<?php

require 'myClasses/Hit.class.php';

$objHit = New \myClasses\Hit;

if(key_exists('lat', $_POST)){
    $objHit->set_lat($_POST['lat']);
}

if(key_exists('lng', $_POST)){
    $objHit->set_lng($_POST['lng']);
}

$data = array(
    'gemeinde' => $objHit->get_gemeinde(),
    'bundesland' => $objHit->get_bundesland(),
    'staat' => $objHit->get_staat()
);

// echo json_encode($data);

echo implode('|', $data);


?>