<?php
/**
 * config.php
 * Holds database configuration details.
 */

$host = '127.0.0.1';
$db   = 'grading_system';
$user = 'root';
$pass = ''; // Default laragon password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If DB doesn't exist, we fallback to connect without db, 
    // but the system assumes DB is created. So we throw exception.
    die("Database connection failed. Please ensure MySQL is running and the database ' grading_system ' is created. Error: " . $e->getMessage());
}
?>
