<?php

namespace controllers;

use flight;

class Test
{
    public static function test($name, $id)
    {
        $users = new \models\Users();

        Flight::render('test_test.php', array('model' => $users->getResults()));
    }
}
