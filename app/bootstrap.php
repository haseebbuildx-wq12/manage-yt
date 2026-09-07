<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

use App\Core\Env;
use App\Core\Database;

Env::load(BASE_PATH . '/.env');
Database::init();
