<?php

namespace controllers;

use flight;

class Index
{
    public static function start()
    {
        Flight::render('index_start');
    }
}
