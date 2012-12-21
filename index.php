<?php
require 'flight/Flight.php';
require 'flight/util/PDOWrapper.php';

//Flight::register('db', 'PDOWrapper', array('mysql:host=127.0.0.1;port=3306;dbname=test','root','', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')));

Flight::route('/', function(){

//    $db = Flight::db();
//    $results = $db->select("phpc_config");
    echo 'Hello world';
});

Flight::start();