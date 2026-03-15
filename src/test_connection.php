<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "DB_HOST: " . (getenv('DB_HOST') ?: 'not set') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'not set') . "\n";

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'pokegame_admin';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "CONNECTION SUCCESSFUL\n";
    $stmt = $pdo->query("SELECT user(), current_user()");
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "CONNECTION FAILED: " . $e->getMessage() . "\n";
}
