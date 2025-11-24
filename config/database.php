<?php

/**
 * Database Configuration
 * Connection settings for MariaDB/MySQL
 */

return [
    // Database driver
    'driver' => getenv('DB_CONNECTION') ?: 'mysql',
    
    // Connection details
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => (int)(getenv('DB_PORT') ?: 3306),
    'database' => getenv('DB_DATABASE') ?: 'trevio',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    
    // Charset and collation
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    'collation' => getenv('DB_COLLATION') ?: 'utf8mb4_unicode_ci',
    
    // PDO options for security and performance
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Real prepared statements
        PDO::ATTR_PERSISTENT         => false, // No persistent connections
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_STRINGIFY_FETCHES  => false, // Return native types
        PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL,
    ],
    
    // Connection timeout (seconds)
    'timeout' => 10,
    
    // Enable query logging for debugging
    'log_queries' => filter_var(getenv('LOG_QUERIES'), FILTER_VALIDATE_BOOLEAN),
    
    // Slow query threshold (seconds)
    'slow_query_threshold' => 1.0,
];
