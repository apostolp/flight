<?php

namespace console;

use flight;

class Test
{
    public static function run($args)
    {
        $users = new \models\Users();
        var_dump($users->getResults());
        //var_dump($args);
    }
}
