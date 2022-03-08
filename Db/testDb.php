<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>

    <ul>
        <li><a href="/index.php">Main</a></li>
        <li><a href="/Db/testDb.php">Check DB connect</a></li>
    </ul>

<?php

require_once __DIR__ . '/connectDb.php';

if ($pdo) {
    echo "Connected to the $db database successfully!";
} else {
    echo "ERROR!";
}
//$host= 'postgres';
//$db = 'rabbit_db';
//$user = 'rabbit_user';
//$password = 'secret';
//
//try {
//    $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
//    // make a database connection
//    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
//
//    if ($pdo) {
//        echo "Connected to the $db database successfully!";
//    }
//} catch (PDOException $e) {
//    die($e->getMessage());
//}
//finally {
//    if ($pdo) {
//        $pdo = null;
//    }
//}