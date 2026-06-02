<?php
// Database configuration 
$host     = 'localhost';
$dbname   = 'athleticeats';
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<p style="color:red;font-family:sans-serif;padding:2rem;">
        <strong>Database connection failed:</strong> ' . htmlspecialchars($e->getMessage()) . '
        <br><br>Make sure XAMPP MySQL is running and you have run setup.sql in phpMyAdmin.
    </p>');
}
