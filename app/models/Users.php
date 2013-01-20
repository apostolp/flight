<?php

namespace models;

use flight;

class Users
{

    /**
     * @var flight\util\PDOWrapper $db
     */
    public $db;

    public function __construct()
    {
        $this->db = Flight::db();
    }

    public function getResults()
    {
        $results = $this->db->select("cds");

        return $results;
    }
}
