<?php
/**
 * Database connection settings — TEMPLATE.
 * Copy this file to db.php and fill in your real credentials.
 * db.php is gitignored so real credentials never get committed.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'resqfood');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Base URL of the app, relative to your server root.
 * - If this project lives in htdocs/resqfood -> keep '/resqfood'
 * - If this project lives directly in htdocs, or at your deployed domain root -> change to ''
 */
define('BASE_URL', '/resqfood');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
