<?php

$config = dirname(__FILE__) . '/app/config/main.php';

require 'flight/Flight.php';

Flight::route('/', function(){

    $db = Flight::db();
    $results = $db->select("cds");

	/*$db2 = Flight::db2();
    $results2 = $db2->select("phpc_config");*/

});

Flight::start();