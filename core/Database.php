<?php
// trevio-project/core/Database.php

require_once __DIR__ . '/../config/database.php';

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_DATABASE . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $this->connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        } catch (\PDOException $e) {
            $error_message = "Database Error: " . $e->getMessage();
            error_log($error_message);
            throw new \Exception("Database connection failed. Check your configuration.");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}