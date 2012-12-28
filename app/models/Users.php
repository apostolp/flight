<?php

namespace models;

use flight;

class Users
{

    public function getResults()
    {
        $db = Flight::db();
        $results = $db->select("cds");

        return $results;
    }
}
