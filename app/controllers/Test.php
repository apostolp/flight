<?php

namespace controllers;

use flight;

class Test
{
    public static function test($name, $id)
    {
        $users = new \models\Users();

        echo '<xmp>';
        print_r($users->getResults());
        echo '</xmp>';
    }
}
