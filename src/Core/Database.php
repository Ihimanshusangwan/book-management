<?php
namespace Core;

use mysqli;
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $this->connection = new mysqli(
            $config['db_host'],
            $config['db_user'],
            $config['db_password'],
            $config['db_name']
        );

        if ($this->connection->connect_error) {
            die('Database connection failed: ' . $this->connection->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}