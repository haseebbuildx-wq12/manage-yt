<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "STEP 1: PHP WORKING<br>";

require __DIR__ . '/app/bootstrap.php';

echo "STEP 2: BOOTSTRAP WORKING<br>";

try {

    $router = new \App\Core\Router();

    echo "STEP 3: ROUTER CLASS WORKING<br>";

} catch (\Throwable $e) {

    echo "<h2>ERROR FOUND</h2>";

    echo "<pre>";
    echo "Message: " . htmlspecialchars($e->getMessage()) . "\n\n";
    echo "File: " . htmlspecialchars($e->getFile()) . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";

}