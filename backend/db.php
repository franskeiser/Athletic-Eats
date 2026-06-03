<?php
require_once __DIR__ . '/config.php';

if (DEBUG_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        die('<p style="color:red;font-family:sans-serif;padding:2rem;">
            <strong>Database connection failed:</strong> ' . htmlspecialchars($e->getMessage()) . '
        </p>');
    }
    http_response_code(503);
    die('<p style="font-family:sans-serif;padding:2rem;">
        Service temporarily unavailable. Please try again later.
    </p>');
}
