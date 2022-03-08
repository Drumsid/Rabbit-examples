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
}
