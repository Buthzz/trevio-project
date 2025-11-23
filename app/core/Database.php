<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Class - Secure PDO Wrapper
 * Handles all database operations with prepared statements
 */
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $charset;
    
    private $dbh; // Database Handler
    private $stmt; // Statement
    private $error;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct() {
        // Load from environment or config
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->user = getenv('DB_USERNAME') ?: 'root';
        $this->pass = getenv('DB_PASSWORD') ?: '';
        $this->dbname = getenv('DB_DATABASE') ?: 'trevio';
        $this->charset = getenv('DB_CHARSET') ?: 'utf8mb4';
        
        // Set DSN (Data Source Name)
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        
        // Set PDO options for security and performance
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // Use real prepared statements
            PDO::ATTR_PERSISTENT         => false, // Don't use persistent connections
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE utf8mb4_unicode_ci"
        ];
        
        // Create PDO instance with error handling
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            
            // Log error securely (don't expose credentials)
            error_log("Database Connection Error: " . $e->getMessage());
            
            // In production, show generic error
            if (getenv('APP_ENV') === 'production') {
                die("Database connection failed. Please contact administrator.");
            } else {
                die("Database Error: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Prepare SQL query with placeholders
     * @param string $query SQL query with named placeholders
     */
    public function query($query) {
        try {
            $this->stmt = $this->dbh->prepare($query);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Query Preparation Error: " . $e->getMessage());
            error_log("Query: " . $query);
            throw $e;
        }
    }
    
    /**
     * Bind values to prepared statement
     * @param string $param Parameter identifier
     * @param mixed $value Value to bind
     * @param int|null $type PDO parameter type
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        try {
            $this->stmt->bindValue($param, $value, $type);
        } catch (PDOException $e) {
            error_log("Bind Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Execute prepared statement
     * @return bool
     */
    public function execute() {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Execute Error: " . $e->getMessage());
            
            // Log query for debugging (remove in production)
            if (getenv('LOG_QUERIES') === 'true') {
                error_log("Failed Query: " . $this->stmt->queryString);
            }
            
            throw $e;
        }
    }
    
    /**
     * Fetch multiple rows as array
     * @return array
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Fetch single row as associative array
     * @return array|false
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Get row count
     * @return int
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Get last inserted ID
     * @return string
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
    
    /**
     * Begin transaction
     * @return bool
     */
    public function beginTransaction() {
        try {
            return $this->dbh->beginTransaction();
        } catch (PDOException $e) {
            error_log("Begin Transaction Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Commit transaction
     * @return bool
     */
    public function commit() {
        try {
            return $this->dbh->commit();
        } catch (PDOException $e) {
            error_log("Commit Transaction Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rollback transaction
     * @return bool
     */
    public function rollBack() {
        try {
            return $this->dbh->rollBack();
        } catch (PDOException $e) {
            error_log("Rollback Transaction Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if in transaction
     * @return bool
     */
    public function inTransaction() {
        return $this->dbh->inTransaction();
    }
    
    /**
     * Get debug dump of statement
     * @return string|null
     */
    public function debugDumpParams() {
        if ($this->stmt) {
            ob_start();
            $this->stmt->debugDumpParams();
            return ob_get_clean();
        }
        return null;
    }
    
    /**
     * Close cursor to free up resources
     */
    public function closeCursor() {
        if ($this->stmt) {
            $this->stmt->closeCursor();
        }
    }
    
    /**
     * Get PDO instance (for advanced usage)
     * @return PDO
     */
    public function getPDO() {
        return $this->dbh;
    }
    
    /**
     * Destructor - Close database connection
     */
    public function __destruct() {
        $this->stmt = null;
        $this->dbh = null;
    }
}
