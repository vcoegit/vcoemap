<?php  

//echo hash('md4', 'Stringedingeding' . 'salt&pepper');

//echo parse_url();

?>

<?php
// $uri = $_SERVER['REQUEST_URI'];
// echo $uri; // Outputs: URI
 
// $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
 
// $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// echo $url; // Outputs: Full URL
 
// $query = $_SERVER['QUERY_STRING'];
// echo $query; // Outputs: Query String
?>

<?php

    //echo __DIR__ . 'häh';
    //echo dirname(__FILE__);
    // echo __FILE__;
    echo "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

?>