<?php

/**
 * Application config
 *
 * @author   Pavel Paschenko
 * @created  21.12.12 21:46
 * @return   array
 */
return array(    

    'dbFactory' =>
        array(
        'db' => array(
            'class' => 'PDOWrapper',
            'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=cdcol',
            'username' => 'root',
            'password' => '',
            'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
            ),
        'db2' => array(
            'class' => 'PDOWrapper',
            'connectionString' => 'mysql:host=127.0.0.1;port=3306;dbname=calendar',
            'username' => 'root',
            'password' => '',
            'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
        ),

	),
);