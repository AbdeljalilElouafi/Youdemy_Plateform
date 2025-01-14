<?php
namespace Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private static $instance = null;
    private $connection = null;

    private function __construct() {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

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


    private function __clone() {}

    public function __wakeup() {}
}