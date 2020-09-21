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
    'bezirk' => $objHit->get_bezirk(),
    'bundesland' => $objHit->get_bundesland(),
    'staat' => $objHit->get_staat(),
    'gemeinde' => $objHit->get_gemeinde()
);

// echo json_encode($data);

echo implode('|', $data);


?>