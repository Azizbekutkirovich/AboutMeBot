<?php

$url = getenv('MYSQL_URL');

if (!$url) {
    die("MYSQL_URL environment variable aniqlanmadi!");
}

$parts = parse_url($url);
$host = $parts['host'];
$port = $parts['port'];
$user = $parts['user'];
$pass = $parts['pass'];
$db   = ltrim($parts['path'], '/');

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);

} catch (PDOException $e) {
    echo "Xatolik: ".$e->getMessage();
    exit;
}