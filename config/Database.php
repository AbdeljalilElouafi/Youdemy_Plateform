<?php

namespace Config;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection = null;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . $_ENV['HOST'] . ";dbname=" . $_ENV['DATABASE'],
                $_ENV['USERNAME'],
                $_ENV['PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserialize
    private function __wakeup() {}
}