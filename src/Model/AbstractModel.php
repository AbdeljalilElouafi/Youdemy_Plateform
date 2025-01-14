<?php

namespace App\Model;

use Config\Database;

abstract class AbstractModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}