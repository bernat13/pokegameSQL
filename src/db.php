<?php
session_start();

function get_db_connection($user = null, $pass = null) {
    $host = getenv('DB_HOST') ?: 'localhost';
    $db   = getenv('DB_NAME') ?: 'pokegame_admin';
    
    // If no credentials provided, use session ones
    if (!$user && isset($_SESSION['db_user'])) {
        $user = $_SESSION['db_user'];
        $pass = $_SESSION['db_pass'];
        // Override default DB with assigned one if exists
        if (isset($_SESSION['db_name'])) {
            $db = $_SESSION['db_name'];
        }
    }


    // Debug log for every connection
    $logMsg = "[" . date('Y-m-d H:i:s') . "] ATTEMPT CONNECT: Host=$host, User=$user, DB=$db, PassLen=" . strlen($pass) . "\n";
    file_put_contents('/tmp/db_connect.log', $logMsg, FILE_APPEND);

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
?>
