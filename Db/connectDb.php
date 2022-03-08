<?php
$host= 'postgres';
$db = 'rabbit_db';
$user = 'rabbit_user';
$password = 'secret';

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
    // make a database connection
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

} catch (PDOException $e) {
    die($e->getMessage());
}
