<?php

/**
 * Database Configuration
 * Connection settings for MariaDB/MySQL
 */

return [
    // Database driver
    'driver' => $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: 'mysql',

    // Connection details
    'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost',
    'port' => (int) ($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306),
    'database' => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'trevio',
    'username' => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '',

    // Charset and collation
    'charset' => $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4',
    'collation' => $_ENV['DB_COLLATION'] ?? getenv('DB_COLLATION') ?: 'utf8mb4_unicode_ci',

    // PDO options for security and performance
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, // Real prepared statements
        PDO::ATTR_PERSISTENT => false, // No persistent connections
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::ATTR_STRINGIFY_FETCHES => false, // Return native types
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
    ],

    // Connection timeout (seconds)
    'timeout' => 10,

    // Enable query logging for debugging
    'log_queries' => filter_var($_ENV['LOG_QUERIES'] ?? getenv('LOG_QUERIES'), FILTER_VALIDATE_BOOLEAN),

    // Slow query threshold (seconds)
    'slow_query_threshold' => 1.0,
];
