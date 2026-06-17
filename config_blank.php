<?php
// Database configuration (local PostgreSQL server)
$host = '';
$port = '';
$dbname = '';
$username = '';
$password = '';

// Admin password for viewing poems (change this to a secure password)
$admin_password = '';

// Additional PDO options for better performance and security
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password, $options);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed");
}
?>
