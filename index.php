<?php

$config = dirname(__FILE__) . '/app/config/main.php';

require 'flight/Flight.php';

Flight::route('/', function(){

    //$db = Flight::db();
    //$results = $db->select("cds");
    echo 'Hello world';

});

Flight::start();