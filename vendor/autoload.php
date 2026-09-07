<?php
// Phase 1 offline package placeholder.
// Production ZIP must contain the Composer-generated vendor tree.
// Run `composer install --no-dev --optimize-autoloader` during build/packaging.
// This repository does not require Composer on the target server.
spl_autoload_register(function(string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});
require __DIR__ . '/../app/Helpers/functions.php';
