<?php
//Database credentials
// Settings/db_cred.php

// Load environment variables from db_config.env if it exists
if (file_exists(__DIR__ . '/../db_config.env')) {
    $lines = file(__DIR__ . '/../db_config.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'dbforlab');

if (!defined("SERVER")) {
    define("SERVER", $_ENV['DB_HOST'] ?? "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", $_ENV['DB_USER'] ?? "root");
}

if (!defined("PASSWD")) {
    define("PASSWD", $_ENV['DB_PASS'] ?? "Stacks4lyf!$");
}

if (!defined("DATABASE")) {
    define("DATABASE", $_ENV['DB_NAME'] ?? "ecommerce_2025A_lady_hagan");
}
?>